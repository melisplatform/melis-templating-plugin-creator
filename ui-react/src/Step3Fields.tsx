import type { Context, FieldSpec, StepErrors } from './tpc-api'
import { Field, LockIcon, card, disabled as disabledCss, inputCss, textareaCss, useT } from './ui'

/**
 * Étape 3 — les propriétés éditables du plugin.
 *
 * Le champ 1 est TOUJOURS `template_path` : il est affiché en lecture seule et RECALCULÉ côté
 * serveur (le client ne peut pas l'usurper). Les champs 2..N sont libres.
 *
 * Le widget de « valeur par défaut » dépend du type d'affichage — c'est le pendant React de
 * `fieldFormInit()` du JS legacy, en beaucoup plus simple : ici on ne saisit qu'une valeur par
 * défaut, pas la vraie donnée, donc un `<input type=date>` suffit là où le legacy montait un
 * datetimepicker jQuery, et un `<textarea>` remplace TinyMCE.
 */

const emptyField = (): FieldSpec => ({
  tpc_field_name: '',
  tpc_field_display_type: '',
  tpc_field_is_required: '0',
  tpc_field_default_options: '',
  tpc_field_default_value: '',
})

/** Un `fieldCount` de N ⇒ exactement N specs, la 1re étant template_path (placeholder côté client). */
export function resizeFields(fields: FieldSpec[], count: number): FieldSpec[] {
  const next = fields.slice(0, count)
  while (next.length < count) next.push(emptyField())
  return next
}

function DefaultValueInput({ spec, onChange, off }: { spec: FieldSpec; onChange: (v: string) => void; off: boolean }) {
  const common = { value: spec.tpc_field_default_value, disabled: off, onChange: (e: { target: { value: string } }) => onChange(e.target.value) }

  switch (spec.tpc_field_display_type) {
    case 'Dropdown': {
      const options = spec.tpc_field_default_options.split(',').map((o) => o.trim()).filter(Boolean)
      return (
        <select style={inputCss} {...common}>
          <option value="" />
          {options.map((o) => <option key={o} value={o}>{o}</option>)}
        </select>
      )
    }
    case 'DatePicker':
      return <input type="date" style={inputCss} {...common} />
    case 'DateTimePicker':
      return <input type="datetime-local" style={inputCss} {...common} />
    case 'NumericInput':
    case 'PageInput':
      // Le serveur applique un validateur `Digits` sur ces deux types (règle legacy).
      return <input type="number" inputMode="numeric" style={inputCss} {...common} />
    case 'Switch':
      return (
        <select style={inputCss} {...common}>
          <option value="1">On</option>
          <option value="0">Off</option>
        </select>
      )
    case 'Textarea':
    case 'MelisCoreTinyMCE':
      return <textarea style={textareaCss} {...common} />
    default:
      return <input style={inputCss} {...common} />
  }
}

export default function Step3Fields({ ctx, fields, onChange, errors, readOnly, templatePath }: {
  ctx: Context
  fields: FieldSpec[]
  onChange: (f: FieldSpec[]) => void
  errors: StepErrors
  readOnly: boolean
  templatePath: string
}) {
  const t = useT()
  const count = fields.length
  const mainErrors = (errors.main ?? {}) as Record<string, { messages: string[] }>

  const setCount = (n: number) => {
    if (!Number.isFinite(n)) return
    onChange(resizeFields(fields, Math.max(1, Math.min(ctx.maxFields, Math.trunc(n)))))
  }
  const patch = (i: number, part: Partial<FieldSpec>) =>
    onChange(fields.map((f, idx) => (idx === i ? { ...f, ...part } : f)))

  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 16, maxWidth: 860 }}>
      <p style={{ margin: 0, fontSize: 13, color: 'var(--color-muted-foreground)' }}>{t('s3_desc')}</p>

      <div style={{ ...card, padding: 20 }}>
        <Field label={t('s3_count')} required hint={t('s3_count_hint', { max: ctx.maxFields })}
               error={mainErrors.tpc_main_property_field_count?.messages}>
          <input type="number" min={1} max={ctx.maxFields} style={inputCss} value={count} disabled={readOnly}
                 onChange={(e) => setCount(Number(e.target.value))} />
        </Field>
      </div>

      {fields.map((spec, i) => {
        const locked = i === 0
        const fieldErrors = (errors[`field_${i + 1}`] ?? {}) as Record<string, { messages: string[] }>
        const isDropdown = spec.tpc_field_display_type === 'Dropdown'

        return (
          <div key={i} style={{ ...card, padding: 20, display: 'flex', flexDirection: 'column', gap: 16 }}>
            <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
              <span style={{ fontSize: 14, fontWeight: 600 }}>{t('s3_field', { n: i + 1 })}</span>
              {locked && (
                <span style={{ display: 'inline-flex', alignItems: 'center', gap: 4, fontSize: 11, padding: '2px 8px', borderRadius: 999, background: 'color-mix(in srgb, var(--color-muted,#888) 18%, transparent)', color: 'var(--color-muted-foreground)' }}>
                  <LockIcon />{t('s3_locked')}
                </span>
              )}
            </div>

            {locked ? (
              // template_path : affiché pour information, jamais posté (le serveur le recalcule).
              <div style={{ ...disabledCss, display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 14 }}>
                <Field label={t('s3_f_name')}><input style={inputCss} value="template_path" readOnly /></Field>
                <Field label={t('s3_f_default')}><input style={inputCss} value={templatePath} readOnly /></Field>
              </div>
            ) : (
              <>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 160px', gap: 14 }}>
                  <Field label={t('s3_f_name')} required error={fieldErrors.tpc_field_name?.messages}>
                    <input style={inputCss} value={spec.tpc_field_name} disabled={readOnly}
                           onChange={(e) => patch(i, { tpc_field_name: e.target.value })} placeholder="title" />
                  </Field>

                  <Field label={t('s3_f_type')} required error={fieldErrors.tpc_field_display_type?.messages}>
                    <select style={inputCss} value={spec.tpc_field_display_type} disabled={readOnly}
                            onChange={(e) => patch(i, {
                              tpc_field_display_type: e.target.value,
                              // Changer de type invalide la valeur par défaut (et les options hors Dropdown).
                              tpc_field_default_value: '',
                              tpc_field_default_options: e.target.value === 'Dropdown' ? spec.tpc_field_default_options : '',
                            })}>
                      <option value="">{t('s1_choose')}</option>
                      {ctx.displayTypes.map((d) => <option key={d.value} value={d.value}>{d.label}</option>)}
                    </select>
                  </Field>

                  <Field label={t('s3_f_required')} required error={fieldErrors.tpc_field_is_required?.messages}>
                    <select style={inputCss} value={spec.tpc_field_is_required} disabled={readOnly}
                            onChange={(e) => patch(i, { tpc_field_is_required: e.target.value })}>
                      <option value="1">{t('yes')}</option>
                      <option value="0">{t('no')}</option>
                    </select>
                  </Field>
                </div>

                <div style={{ display: 'grid', gridTemplateColumns: isDropdown ? '1fr 1fr' : '1fr', gap: 14 }}>
                  {isDropdown && (
                    <Field label={t('s3_f_options')} required hint={t('s3_f_options_hint')}
                           error={fieldErrors.tpc_field_default_options?.messages}>
                      <input style={inputCss} value={spec.tpc_field_default_options} disabled={readOnly}
                             onChange={(e) => patch(i, { tpc_field_default_options: e.target.value })}
                             placeholder="Light,Dark blue" />
                    </Field>
                  )}
                  <Field label={t('s3_f_default')} error={fieldErrors.tpc_field_default_value?.messages}>
                    <DefaultValueInput spec={spec} off={readOnly} onChange={(v) => patch(i, { tpc_field_default_value: v })} />
                  </Field>
                </div>
              </>
            )}
          </div>
        )
      })}
    </div>
  )
}
