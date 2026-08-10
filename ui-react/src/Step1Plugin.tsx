import type { Context, FieldErrors, Step1Data } from './tpc-api'
import { Field, Notice, card, inputCss, useT } from './ui'

/**
 * Étape 1 — nom du plugin + destination (nouveau module / module de site existant).
 * Les règles métier (mot réservé PHP, module déjà existant, plugin déjà déclaré) sont validées
 * PAR LE SERVEUR : ici on n'affiche que les messages renvoyés, on ne les duplique pas.
 */
export default function Step1Plugin({ ctx, value, onChange, errors, readOnly }: {
  ctx: Context
  value: Step1Data
  onChange: (v: Step1Data) => void
  errors: FieldErrors
  readOnly: boolean
}) {
  const t = useT()
  const set = (k: keyof Step1Data, v: string) => onChange({ ...value, [k]: v })
  const dest = value.tpc_plugin_destination ?? ''
  const noSiteModules = ctx.siteModules.length === 0

  const radio = (v: string, lbl: string, note?: string, off?: boolean) => (
    <label style={{
      display: 'flex', alignItems: 'flex-start', gap: 10, padding: 12, borderRadius: 8, cursor: off ? 'not-allowed' : 'pointer',
      border: `1px solid ${dest === v ? 'var(--color-primary)' : 'var(--color-border)'}`,
      background: dest === v ? 'color-mix(in srgb, var(--color-primary) 6%, transparent)' : 'transparent',
      opacity: off ? 0.55 : 1, flex: 1,
    }}>
      <input type="radio" name="tpc_plugin_destination" checked={dest === v} disabled={off || readOnly}
             onChange={() => set('tpc_plugin_destination', v)} style={{ marginTop: 2 }} />
      <span>
        <span style={{ display: 'block', fontSize: 14, fontWeight: 500 }}>{lbl}</span>
        {note && <span style={{ display: 'block', fontSize: 12, color: 'var(--color-muted-foreground)', marginTop: 2 }}>{note}</span>}
      </span>
    </label>
  )

  return (
    <div style={{ ...card, padding: 20, display: 'flex', flexDirection: 'column', gap: 18, maxWidth: 720 }}>
      <Field label={t('s1_name')} required error={errors.tpc_plugin_name?.messages}>
        <input style={inputCss} value={value.tpc_plugin_name ?? ''} disabled={readOnly}
               onChange={(e) => set('tpc_plugin_name', e.target.value)} placeholder="HeroBanner" />
      </Field>

      <div>
        <label style={{ display: 'block', fontSize: 13, fontWeight: 500, marginBottom: 6 }}>
          {t('s1_dest')}<span style={{ color: 'var(--color-destructive,#ef4444)', marginLeft: 3 }}>*</span>
        </label>
        <div style={{ display: 'flex', gap: 10 }}>
          {radio('new_module', t('s1_new'))}
          {radio('existing_module', t('s1_existing'), noSiteModules ? t('s1_no_site_modules') : undefined, noSiteModules)}
        </div>
        {errors.tpc_plugin_destination?.messages.map((m, i) => (
          <p key={i} style={{ marginTop: 4, fontSize: 12, color: 'var(--color-destructive,#ef4444)' }}>{m}</p>
        ))}
      </div>

      {dest === 'new_module' && (
        <Field label={t('s1_new_name')} required error={errors.tpc_new_module_name?.messages}>
          <input style={inputCss} value={value.tpc_new_module_name ?? ''} disabled={readOnly}
                 onChange={(e) => set('tpc_new_module_name', e.target.value)} placeholder="MyPlugins" />
        </Field>
      )}

      {dest === 'existing_module' && (
        <Field label={t('s1_existing_name')} required error={errors.tpc_existing_module_name?.messages}>
          <select style={inputCss} value={value.tpc_existing_module_name ?? ''} disabled={readOnly}
                  onChange={(e) => set('tpc_existing_module_name', e.target.value)}>
            <option value="">{t('s1_choose')}</option>
            {ctx.siteModules.map((m) => <option key={m} value={m}>{m}</option>)}
          </select>
        </Field>
      )}

      {readOnly && <Notice tone="warn">{t('readonly_notice')}</Notice>}
    </div>
  )
}
