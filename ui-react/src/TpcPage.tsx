import { useCallback, useEffect, useRef, useState, type ReactNode } from 'react'
import Step1Plugin from './Step1Plugin'
import Step2Texts from './Step2Texts'
import Step3Fields, { resizeFields } from './Step3Fields'
import Step4Translations from './Step4Translations'
import Step5Summary from './Step5Summary'
import Step6Finalize from './Step6Finalize'
import { ViewToggle, type ViewMode } from './ViewToggle'
import { useCaps } from './shared/useCaps'
import {
  ArrowLeft, ArrowRight, BrickStyles, CheckIcon, Notice, Pane, RotateIcon, SpinIcon,
  btnGhost, btnPrimary, card, useT,
} from './ui'
import { FormErrorBanner, type FormIssue } from './shared/melis-form-errors'
import type {
  Context, FieldErrors, FieldSpec, LangTexts, Step1Data, StepErrors, StepResult, TranslationField, TranslationRow, WizardState,
} from './tpc-api'
import {
  fetchContext, fetchState, fetchTranslationFields, resetWizard, saveStep1, saveStep2, saveStep3, saveStep4,
} from './tpc-api'

/* ──────────────────────────────────────────────────────────────────────────────
 * Brique « Templating Plugin Creator » (MelisTemplatingPluginCreator) — full React.
 *
 * L'outil legacy est un ASSISTANT en 6 étapes qui génère du code. On le rebâtit en React natif
 * (formulaires, navigation, récapitulatif), mais TOUTE la logique métier reste côté Melis :
 * validation par les formulaires Laminas d'origine, génération par MelisTemplatingPluginCreatorService.
 * Cf. src/Controller/MelisReactApiTemplatingPluginCreatorController.php.
 *
 * ── Pas de rechargement intempestif ────────────────────────────────────────────
 *  1. La brique est `persistent` (brick.manifest.json) : l'hôte la monte une fois et ne la démonte
 *     plus → quitter l'onglet de l'outil puis y revenir ne perd NI la saisie NI l'étape courante.
 *  2. Les 6 étapes sont des volets montés UNE fois puis cachés en CSS (`<Pane>`), pas un composant
 *     échangé selon l'étape : revenir en arrière ne remonte rien, ne refetch rien, ne perd rien.
 *  3. L'iframe « Old » n'est montée qu'au premier passage sur la vue legacy, puis gardée en
 *     `display:none` → basculer New/Old ne la recharge jamais.
 *  4. `fetchContext`/`fetchState` ne tournent qu'au montage ; `translation-fields` seulement quand
 *     l'étape 3 change (les champs à traduire en dépendent).
 *
 * ── Droits ─────────────────────────────────────────────────────────────────────
 * Les capacités (`config/react.capabilities.php`) masquent ici les contrôles ; le serveur les
 * REFUSE de son côté (denyUnlessCan sur l'onglet ET l'action). Masquer n'est jamais la sécurité.
 * ────────────────────────────────────────────────────────────────────────────── */

const MELIS_KEY = 'melistemplatingplugincreator_tool'
const STEP_COUNT = 6

export default function TpcPage() {
  const t = useT()
  const { can } = useCaps(MELIS_KEY)   // droits avancés (config/react.capabilities.php)

  const [ctx, setCtx] = useState<Context | null>(null)
  const [fatal, setFatal] = useState<string | null>(null)
  const [step, setStep] = useState(1)
  const [busy, setBusy] = useState(false)

  // ── Données de l'assistant (une seule source de vérité, partagée par les volets) ──
  const [step1, setStep1] = useState<Step1Data>({})
  const [step2, setStep2] = useState<Record<string, LangTexts>>({})
  const [fields, setFields] = useState<FieldSpec[]>([])
  const [step4, setStep4] = useState<Record<string, Record<string, TranslationRow>>>({})
  const [thumbnail, setThumbnail] = useState<string | null>(null)
  const [templatePath, setTemplatePath] = useState('')

  const [errors, setErrors] = useState<Record<number, StepErrors>>({})
  const [transFields, setTransFields] = useState<TranslationField[]>([])
  const [transLoading, setTransLoading] = useState(false)
  /** Signature de l'étape 3 ayant produit `transFields` → évite de refetcher sans changement. */
  const transSig = useRef<string>('')

  const [mode, setMode] = useState<ViewMode>('react')
  const [frameLoaded, setFrameLoaded] = useState(false)

  const canEdit = can('wizard') && can('wizard.edit')
  const canSummary = can('summary') && can('summary.list')
  const canGenerate = can('finalization') && can('finalization.create')
  const readOnly = !canEdit
  const isNewModule = step1.tpc_plugin_destination === 'new_module'

  /** Applique un état serveur (montage, reset, retour de la vue legacy). */
  const applyState = useCallback((s: WizardState) => {
    setStep1(s.step1 ?? {})
    setStep2(s.step2 ?? {})
    setFields(resizeFields(s.step3?.fields ?? [], Math.max(1, s.step3?.fieldCount ?? 1)))
    setStep4(
      Object.fromEntries(Object.entries(s.step4 ?? {}).map(([l, rows]) => [l, { ...rows }])),
    )
    setThumbnail(s.thumbnail ?? null)
    setTemplatePath(s.templatePath ?? '')
    setErrors({})
    transSig.current = ''
    setStep(Math.min(STEP_COUNT, (s.completedStep ?? 0) + 1))
  }, [])

  // Chargement initial — UNE fois. La brique étant persistante, cela ne se rejoue jamais.
  useEffect(() => {
    let alive = true
    Promise.all([fetchContext(), fetchState()])
      .then(([c, s]) => { if (!alive) return; setCtx(c); applyState(s) })
      .catch((e: Error) => { if (alive) setFatal(e.message) })
    return () => { alive = false }
  }, [applyState])

  /** Les champs à traduire (étape 4) dérivent de l'étape 3 → refetch seulement si elle a changé. */
  const loadTranslationFields = useCallback(async () => {
    const sig = JSON.stringify(fields.map((f) => [f.tpc_field_name, f.tpc_field_display_type, f.tpc_field_default_options]))
    if (sig === transSig.current) return
    setTransLoading(true)
    try {
      const { fields: tf } = await fetchTranslationFields()
      setTransFields(tf)
      transSig.current = sig
      // Le libellé des options du champ verrouillé (template_path) est imposé, comme dans le legacy.
      const locked = tf.find((f) => f.locked)
      if (locked?.options.length && ctx) {
        setStep4((prev) => {
          const next = { ...prev }
          for (const lang of ctx.languages) {
            const row = { ...(next[lang.locale]?.[locked.key] ?? {}) }
            for (const o of locked.options) row[o.key] = o.value
            next[lang.locale] = { ...(next[lang.locale] ?? {}), [locked.key]: row }
          }
          return next
        })
      }
    } catch (e) {
      setErrors((p) => ({ ...p, 4: { __load: { label: '', messages: [(e as Error).message] } } }))
    } finally {
      setTransLoading(false)
    }
  }, [fields, ctx])

  useEffect(() => { if (step === 4) void loadTranslationFields() }, [step, loadTranslationFields])

  /** Valide + enregistre l'étape courante côté serveur, puis avance. */
  async function next() {
    if (step >= STEP_COUNT) return
    if (readOnly || step === 5) { setStep(step + 1); return }

    setBusy(true)
    try {
      let res: StepResult
      switch (step) {
        case 1: res = await saveStep1(step1); break
        case 2: res = await saveStep2(step2); break
        case 3: res = await saveStep3(fields.length, fields); break
        case 4: res = await saveStep4(step4); break
        default: res = { valid: true, errors: {} }
      }
      setErrors((p) => ({ ...p, [step]: res.errors }))
      if (res.valid) {
        // `template_path` est dérivé du nom du plugin + du module cible : il n'existe qu'une fois
        // l'étape 1 enregistrée, et c'est le SERVEUR qui l'impose (l'étape 3 l'affiche verrouillé).
        if (step === 1) { try { setTemplatePath((await fetchState()).templatePath ?? '') } catch { /* non bloquant */ } }
        setStep(step + 1)
      }
    } catch (e) {
      // Erreur technique OU refus de droits (403) → bandeau, pas d'avancement.
      setErrors((p) => ({ ...p, [step]: { __http: { label: '', messages: [(e as Error).message] } } }))
    } finally {
      setBusy(false)
    }
  }

  async function restart() {
    setBusy(true)
    try { applyState(await resetWizard()) } catch (e) { setFatal((e as Error).message) } finally { setBusy(false) }
  }

  /**
   * La vue « Old » ouvre le tool legacy, dont `renderToolAction()` VIDE le conteneur de session
   * partagé — le brouillon React est donc perdu. On le dit avant de basculer, et au retour on
   * relit l'état serveur pour rester fidèle à la vérité (l'assistant repart de l'étape 1).
   */
  async function switchMode(m: ViewMode) {
    if (m === 'iframe' && !frameLoaded) {
      const draft = !!(step1.tpc_plugin_name || thumbnail || fields.length > 1)
      if (draft && !window.confirm(`${t('title')}\n\n${t('old_resets')}`)) return
      setFrameLoaded(true)
    }
    if (m === 'react' && frameLoaded) {
      try { applyState(await fetchState()) } catch { /* on garde l'état courant */ }
    }
    setMode(m)
  }

  if (fatal) return <Shell><Notice tone="warn">{fatal}</Notice></Shell>
  if (!ctx) return <Shell><div style={{ display: 'flex', gap: 8, alignItems: 'center' }}><SpinIcon />{t('loading')}</div></Shell>

  if (ctx.blocking.length) {
    return (
      <Shell>
        <div style={{ ...card, padding: 20, maxWidth: 720 }}>
          <h2 style={{ margin: '0 0 6px', fontSize: 16, fontWeight: 700 }}>{t('blocking_title')}</h2>
          <p style={{ margin: '0 0 10px', fontSize: 13, color: 'var(--color-muted-foreground)' }}>{t('blocking_desc')}</p>
          <ul style={{ margin: 0, paddingLeft: 18, fontSize: 13 }}>
            {ctx.blocking.map((b, i) => <li key={i} dangerouslySetInnerHTML={{ __html: b }} />)}
          </ul>
        </div>
      </Shell>
    )
  }

  // Erreurs de l'étape courante → liste plate `{ label, message }` : la bannière unifiée NOMME chaque
  // champ manquant/invalide (les `Field` restent surlignés en rouge inline en plus de la bannière).
  const stepErrors = errors[step] ?? {}
  const stepIssues: FormIssue[] = Object.values(stepErrors).flatMap((v) => {
    if (v && typeof v === 'object' && 'messages' in v) {
      const e = v as { label?: string; messages: string[] }
      return e.messages.map((message) => ({ label: e.label || undefined, message }))
    }
    return []
  })

  return (
    <Shell
      toolbar={
        <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
          {canEdit && (
            <button style={btnGhost} onClick={restart} disabled={busy}><RotateIcon />{t('restart')}</button>
          )}
          <ViewToggle mode={mode} onChange={(m) => void switchMode(m)} />
        </div>
      }
    >
      {/* Vue « Old » : outil legacy en iframe, montée une seule fois puis gardée en display:none. */}
      {frameLoaded && (
        <div style={{ ...card, display: mode === 'iframe' ? 'flex' : 'none', flex: 1, minHeight: 620, overflow: 'hidden' }}>
          <iframe src={`/melis/react-tool-page?key=${encodeURIComponent(MELIS_KEY)}`}
                  style={{ flex: 1, width: '100%', border: 0 }} title={t('title')} />
        </div>
      )}

      <div style={{ display: mode === 'react' ? 'flex' : 'none', flexDirection: 'column', gap: 20 }}>
        <Stepper steps={ctx.steps.map((s) => s.name)} current={step} onGo={(n) => n <= step && setStep(n)} />

        {readOnly && <Notice tone="warn">{t('readonly_notice')}</Notice>}
        {stepIssues.length > 0 && <FormErrorBanner title={t('check_fields')} issues={stepIssues} />}

        {/* Volets montés une fois, montrés/cachés en CSS → aucun remontage, aucun refetch. */}
        <Pane show={step === 1}>
          <Step1Plugin ctx={ctx} value={step1} onChange={setStep1} readOnly={readOnly}
                       errors={(errors[1] ?? {}) as FieldErrors} />
        </Pane>
        <Pane show={step === 2}>
          <Step2Texts ctx={ctx} value={step2} onChange={setStep2} thumbnail={thumbnail} onThumbnail={setThumbnail}
                      errors={errors[2] ?? {}} readOnly={readOnly}
                      canUpload={can('thumbnail') && can('thumbnail.create')}
                      canDelete={can('thumbnail') && can('thumbnail.delete')} />
        </Pane>
        <Pane show={step === 3}>
          <Step3Fields ctx={ctx} fields={fields} onChange={setFields} errors={errors[3] ?? {}}
                       readOnly={readOnly} templatePath={templatePath} />
        </Pane>
        <Pane show={step === 4}>
          <Step4Translations fields={transFields} languages={ctx.languages} value={step4} onChange={setStep4}
                             errors={errors[4] ?? {}} readOnly={readOnly} loading={transLoading} />
        </Pane>
        <Pane show={step === 5}>
          {canSummary ? <Step5Summary active={step === 5} /> : <Notice tone="warn">{t('s5_no_access')}</Notice>}
        </Pane>
        <Pane show={step === 6}>
          <Step6Finalize ctx={ctx} isNewModule={isNewModule} canGenerate={canGenerate} onDone={restart}
                         onBack={() => setStep(step - 1)} backDisabled={busy} />
        </Pane>

        {/* Étape 6 : la rangée Back/Générer est portée par Step6Finalize (même alignement que les
            autres étapes). Ici on gère les étapes 1..5. */}
        {step < STEP_COUNT && (
          <div style={{ display: 'flex', gap: 10 }}>
            <button style={{ ...btnGhost, visibility: step > 1 ? 'visible' : 'hidden' }}
                    onClick={() => setStep(step - 1)} disabled={busy}><ArrowLeft />{t('back')}</button>
            <button style={btnPrimary} onClick={() => void next()} disabled={busy}>
              {busy ? <><SpinIcon />{t('saving')}</> : <>{step === 5 ? <CheckIcon /> : null}{t('next')}<ArrowRight /></>}
            </button>
          </div>
        )}
      </div>
    </Shell>
  )
}

/** Conteneur plein-écran, scrollable — même gabarit que les autres briques full-React. */
function Shell({ children, toolbar }: { children: ReactNode; toolbar?: ReactNode }) {
  const t = useT()
  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 20, padding: 24, height: '100%', boxSizing: 'border-box', overflow: 'auto' }}>
      <BrickStyles />
      <div style={{ display: 'flex', alignItems: 'flex-start', justifyContent: 'space-between', gap: 16 }}>
        <div>
          <h1 style={{ fontSize: 20, fontWeight: 700, margin: 0 }}>{t('title')}</h1>
          <p style={{ fontSize: 14, color: 'var(--color-muted-foreground)', margin: '2px 0 0' }}>{t('subtitle')}</p>
        </div>
        {toolbar}
      </div>
      {children}
    </div>
  )
}

/** Fil des étapes : pastille numérotée + libellé, cliquable vers les étapes déjà atteintes. */
function Stepper({ steps, current, onGo }: { steps: string[]; current: number; onGo: (n: number) => void }) {
  return (
    <div style={{ display: 'flex', alignItems: 'center', gap: 4, flexWrap: 'wrap' }}>
      {steps.map((name, i) => {
        const n = i + 1
        const done = n < current
        const active = n === current
        return (
          <div key={name} style={{ display: 'flex', alignItems: 'center', gap: 4 }}>
            <button type="button" onClick={() => onGo(n)} disabled={n > current} style={{
              display: 'inline-flex', alignItems: 'center', gap: 8, padding: '6px 12px', borderRadius: 999, border: 0,
              cursor: n <= current ? 'pointer' : 'default', fontSize: 13, fontWeight: active ? 600 : 500,
              background: active ? 'color-mix(in srgb, var(--color-primary) 12%, transparent)' : 'transparent',
              color: active ? 'var(--color-primary)' : done ? 'var(--color-foreground)' : 'var(--color-muted-foreground)',
            }}>
              <span style={{
                display: 'inline-flex', alignItems: 'center', justifyContent: 'center', width: 22, height: 22, borderRadius: '50%',
                fontSize: 11, fontWeight: 600,
                background: active ? 'var(--color-primary)' : done ? '#22c55e' : 'color-mix(in srgb, var(--color-muted,#888) 20%, transparent)',
                color: active || done ? '#fff' : 'var(--color-muted-foreground)',
              }}>{done ? <CheckIcon /> : n}</span>
              {name}
            </button>
            {n < steps.length && <span style={{ width: 16, height: 1, background: 'var(--color-border)' }} />}
          </div>
        )
      })}
    </div>
  )
}
