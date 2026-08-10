import { useEffect, useState } from 'react'
import type { Context, GenerateResult } from './tpc-api'
import { generatePlugin } from './tpc-api'
import { ArrowLeft, CheckIcon, Notice, SpinIcon, Toggle, btnGhost, btnPrimary, card, hint, inputCss, label, useT } from './ui'
import { FormErrorBanner, koNotify, okNotify } from './shared/melis-form-errors'

/**
 * Étape 6 — finalisation. Le SEUL endroit qui mute la plateforme (écriture de fichiers PHP,
 * activation du module) → gardé par la capacité `finalization` + `finalization.create`, côté
 * serveur ET côté UI. Le bouton n'est même pas rendu sans le droit.
 *
 * `restartRequired` (l'utilisateur a demandé l'activation) déclenche un compte à rebours puis un
 * rechargement complet — la plateforme doit relire ses chemins de modules.
 */
export default function Step6Finalize({ ctx, isNewModule, canGenerate, onDone, onBack, backDisabled }: {
  ctx: Context
  isNewModule: boolean
  canGenerate: boolean
  onDone: () => void
  onBack: () => void
  backDisabled: boolean
}) {
  const t = useT()
  const [site, setSite] = useState('')
  const [activate, setActivate] = useState(true)
  const [busy, setBusy] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [result, setResult] = useState<GenerateResult | null>(null)

  async function run() {
    setBusy(true)
    setError(null)
    try {
      setResult(await generatePlugin(site, activate))
      okNotify(t('ok_title'))
    } catch (e) {
      const message = e instanceof Error ? e.message : String(e)
      setError(message)
      koNotify(t('generate_failed'), message)
    } finally {
      setBusy(false)
    }
  }

  if (result) return <Success result={result} onDone={onDone} />

  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 16, maxWidth: 720 }}>
      <p style={{ margin: 0, fontSize: 13, color: 'var(--color-muted-foreground)' }}>
        {isNewModule ? t('s6_desc_new') : t('s6_desc_existing')}
      </p>

      {!canGenerate && <Notice tone="warn">{t('s6_no_access')}</Notice>}
      {error && <FormErrorBanner title={t('generate_failed')} issues={error} html />}

      <div style={{ ...card, padding: 20, display: 'flex', flexDirection: 'column', gap: 18 }}>
        {isNewModule && (
          <div>
            <label style={label}>{t('s6_site')}</label>
            <select style={inputCss} value={site} onChange={(e) => setSite(e.target.value)} disabled={!canGenerate || busy}>
              <option value="">{t('none')}</option>
              {ctx.sites.map((s) => <option key={s.name} value={s.name}>{s.label}</option>)}
            </select>
          </div>
        )}

        <div style={{ display: 'flex', alignItems: 'flex-start', gap: 12 }}>
          <Toggle on={activate} onClick={() => setActivate((v) => !v)} disabled={!canGenerate || busy} />
          <div>
            <span style={{ fontSize: 14, fontWeight: 500 }}>{t('s6_activate')}</span>
            <p style={hint}>{t('s6_activate_note')}</p>
          </div>
        </div>
      </div>

      {/* Même rangée/alignement que le Back+Next des autres étapes ([Back ghost][primaire]). */}
      <div style={{ display: 'flex', gap: 10 }}>
        <button style={btnGhost} onClick={onBack} disabled={backDisabled}><ArrowLeft />{t('back')}</button>
        {canGenerate && (
          <button style={btnPrimary} onClick={run} disabled={busy}>
            {busy ? <><SpinIcon />{t('s6_generating')}</> : <><CheckIcon />{t('s6_generate')}</>}
          </button>
        )}
      </div>
    </div>
  )
}

/** Écran de succès : avertissements non bloquants + compte à rebours de rechargement. */
function Success({ result, onDone }: { result: GenerateResult; onDone: () => void }) {
  const t = useT()
  const [left, setLeft] = useState(5)

  useEffect(() => {
    if (!result.restartRequired) return
    if (left <= 0) { window.location.reload(); return }
    const id = window.setTimeout(() => setLeft((n) => n - 1), 1000)
    return () => window.clearTimeout(id)
  }, [result.restartRequired, left])

  return (
    <div style={{ ...card, padding: 28, display: 'flex', flexDirection: 'column', gap: 14, alignItems: 'flex-start', maxWidth: 720 }}>
      <div style={{ display: 'inline-flex', alignItems: 'center', justifyContent: 'center', width: 44, height: 44, borderRadius: '50%', background: 'color-mix(in srgb, #22c55e 18%, transparent)', color: '#16a34a' }}>
        <CheckIcon />
      </div>
      <h2 style={{ margin: 0, fontSize: 18, fontWeight: 700 }}>{t('ok_title')}</h2>
      <div style={{ fontSize: 14, color: 'var(--color-muted-foreground)' }}>
        <div>{t('ok_module', { m: result.module })}</div>
        <div>{t('ok_plugin', { p: result.plugin })}</div>
      </div>

      {result.notices.map((n, i) => <Notice key={i} tone="warn"><span dangerouslySetInnerHTML={{ __html: n }} /></Notice>)}

      {result.restartRequired
        ? <p style={{ margin: 0, fontSize: 14 }}>{t('ok_reload', { n: left })}</p>
        : <p style={{ margin: 0, fontSize: 14, color: 'var(--color-muted-foreground)' }}>{t('ok_manual')}</p>}

      <button style={btnGhost} onClick={onDone}>{t('ok_new')}</button>
    </div>
  )
}
