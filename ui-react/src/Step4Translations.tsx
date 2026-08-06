import { useState } from 'react'
import type { Language, StepErrors, TranslationField, TranslationRow } from './tpc-api'
import { Field, LockIcon, SpinIcon, card, inputCss, LangTabs, textareaCss, useT } from './ui'

/**
 * Étape 4 — libellé + infobulle de chaque propriété, par langue.
 *
 * La liste des champs (et surtout les CLÉS de libellé des options de Dropdown, calculées par
 * `removeNonAlphaNumeric()` côté PHP) vient de `GET /tpc/translation-fields` : la brique ne
 * reproduit pas cette normalisation en JS, elle la consomme.
 *
 * Règle métier reprise du legacy : un champ est valide dès qu'UNE langue le renseigne
 * complètement — d'où la pastille verte sur l'onglet de langue complet.
 */
export default function Step4Translations({ fields, languages, value, onChange, errors, readOnly, loading }: {
  fields: TranslationField[]
  languages: Language[]
  value: Record<string, Record<string, TranslationRow>>
  onChange: (v: Record<string, Record<string, TranslationRow>>) => void
  errors: StepErrors
  readOnly: boolean
  loading: boolean
}) {
  const t = useT()
  const [lang, setLang] = useState(languages[0]?.locale ?? 'en_EN')

  if (loading) {
    return <div style={{ ...card, padding: 24, display: 'flex', alignItems: 'center', gap: 8, maxWidth: 720 }}><SpinIcon />{t('loading')}</div>
  }

  const set = (fieldKey: string, k: string, v: string) =>
    onChange({ ...value, [lang]: { ...(value[lang] ?? {}), [fieldKey]: { ...(value[lang]?.[fieldKey] ?? {}), [k]: v } } })

  /** Une langue est « complète » si chaque champ a son libellé et chaque option son libellé. */
  const complete = (locale: string) =>
    fields.every((f) => {
      const row = value[locale]?.[f.key]
      return !!row?.tpc_field_label?.trim() && f.options.every((o) => !!row[o.key]?.trim())
    })

  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 16, maxWidth: 860 }}>
      <p style={{ margin: 0, fontSize: 13, color: 'var(--color-muted-foreground)' }}>{t('s4_desc')}</p>
      <LangTabs langs={languages} active={lang} onChange={setLang} filled={complete} />

      {fields.map((f) => {
        // `errors[field_N]` est indexé par langue : { <locale>: { champ: {messages} } }.
        const byLocale = (errors[f.key] ?? {}) as Record<string, Record<string, { messages: string[] }>>
        const fieldErrors = byLocale[lang] ?? {}
        const row = value[lang]?.[f.key] ?? {}

        return (
          <div key={f.key} style={{ ...card, padding: 20, display: 'flex', flexDirection: 'column', gap: 14 }}>
            <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
              <code style={{ fontSize: 13, fontWeight: 600 }}>{f.name}</code>
              <span style={{ fontSize: 11, color: 'var(--color-muted-foreground)' }}>{f.displayType}</span>
              {f.locked && (
                <span style={{ display: 'inline-flex', alignItems: 'center', gap: 4, fontSize: 11, padding: '2px 8px', borderRadius: 999, background: 'color-mix(in srgb, var(--color-muted,#888) 18%, transparent)', color: 'var(--color-muted-foreground)' }}>
                  <LockIcon />{t('s3_locked')}
                </span>
              )}
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 14 }}>
              <Field label={t('s4_label')} required error={fieldErrors.tpc_field_label?.messages}>
                <input style={inputCss} value={row.tpc_field_label ?? ''} disabled={readOnly}
                       onChange={(e) => set(f.key, 'tpc_field_label', e.target.value)} />
              </Field>
              <Field label={t('s4_tooltip')} error={fieldErrors.tpc_field_tooltip?.messages}>
                <textarea style={{ ...textareaCss, minHeight: 40, height: 40 }} value={row.tpc_field_tooltip ?? ''} disabled={readOnly}
                          onChange={(e) => set(f.key, 'tpc_field_tooltip', e.target.value)} />
              </Field>
            </div>

            {f.options.length > 0 && (
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 14 }}>
                {f.options.map((o) => (
                  <Field key={o.key} label={t('s4_option_label', { v: o.value })} required error={fieldErrors[o.key]?.messages}>
                    <input style={inputCss} value={row[o.key] ?? ''} disabled={readOnly || f.locked}
                           onChange={(e) => set(f.key, o.key, e.target.value)} />
                  </Field>
                ))}
              </div>
            )}
          </div>
        )
      })}
    </div>
  )
}
