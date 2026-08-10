import { useEffect, useState, type ReactNode } from 'react'
import type { Summary } from './tpc-api'
import { fetchSummary } from './tpc-api'
import { Notice, SpinIcon, card, td, th, useT } from './ui'

/**
 * Étape 5 — récapitulatif en lecture seule (étapes 1 → 4), lu depuis `GET /tpc/summary`.
 * Endpoint gardé par les capacités `summary` + `summary.list` : si l'utilisateur ne les a pas,
 * l'étape est retirée du parcours par le parent — ce composant n'est alors jamais monté.
 *
 * Rechargé à chaque ENTRÉE dans l'étape (`active`), pas à chaque rendu : le récapitulatif doit
 * refléter les étapes précédentes, mais revenir sur l'étape ne doit rien perdre ailleurs.
 */
export default function Step5Summary({ active }: { active: boolean }) {
  const t = useT()
  const [data, setData] = useState<Summary | null>(null)
  const [error, setError] = useState<string | null>(null)
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    if (!active) return
    let alive = true
    setLoading(true)
    fetchSummary()
      .then((d) => { if (alive) { setData(d); setError(null) } })
      .catch((e: Error) => { if (alive) setError(e.message) })
      .finally(() => { if (alive) setLoading(false) })
    return () => { alive = false }
  }, [active])

  if (loading && !data) return <div style={{ ...card, padding: 24, display: 'flex', gap: 8, alignItems: 'center', maxWidth: 720 }}><SpinIcon />{t('loading')}</div>
  if (error) return <Notice tone="warn">{error}</Notice>
  if (!data) return null

  const langName = (locale: string) => data.languages.find((l) => l.locale === locale)?.name ?? locale

  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 16, maxWidth: 860 }}>
      <p style={{ margin: 0, fontSize: 13, color: 'var(--color-muted-foreground)' }}>{t('s5_desc')}</p>

      <div style={{ ...card, padding: 20, display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 16 }}>
        <Kv label={t('s5_plugin')} value={data.step1.tpc_plugin_name ?? '—'} />
        <Kv label={t('s5_target')} value={data.targetModule ?? '—'} />
        <Kv label={t('s5_tpl')} value={data.templatePath ?? '—'} mono />
      </div>

      {data.thumbnail && (
        <div style={{ ...card, padding: 20 }}>
          <img src={data.thumbnail} alt="" style={{ width: 190, height: 100, objectFit: 'cover', borderRadius: 8, border: '1px solid var(--color-border)' }} />
        </div>
      )}

      <Section title={t('s5_texts')}>
        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
          <tbody>
            {Object.entries(data.step2).map(([locale, texts]) => (
              <tr key={locale}>
                <td style={{ ...td, width: 140, color: 'var(--color-muted-foreground)' }}>{langName(locale)}</td>
                <td style={td}>
                  <strong>{texts.tpc_plugin_title}</strong>
                  {texts.tpc_plugin_desc && <div style={{ color: 'var(--color-muted-foreground)', marginTop: 2 }}>{texts.tpc_plugin_desc}</div>}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </Section>

      <Section title={t('s5_fields')}>
        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
          <thead><tr><th style={th}>#</th><th style={th}>Name</th><th style={th}>Type</th><th style={th}>{t('required')}</th><th style={th}>Default</th></tr></thead>
          <tbody>
            {data.step3.fields.map((f, i) => (
              <tr key={i}>
                <td style={{ ...td, color: 'var(--color-muted-foreground)' }}>{i + 1}</td>
                <td style={td}><code>{f.tpc_field_name}</code></td>
                <td style={td}>{f.tpc_field_display_type}</td>
                <td style={td}>{f.tpc_field_is_required === '1' ? t('yes') : t('no')}</td>
                <td style={{ ...td, color: 'var(--color-muted-foreground)' }}>{f.tpc_field_default_value || '—'}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </Section>

      <Section title={t('s5_translations')}>
        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
          <tbody>
            {Object.entries(data.step4).map(([locale, rows]) => (
              <tr key={locale}>
                <td style={{ ...td, width: 140, color: 'var(--color-muted-foreground)' }}>{langName(locale)}</td>
                <td style={td}>
                  {Object.entries(rows).map(([key, row]) => (
                    <div key={key} style={{ marginBottom: 2 }}>
                      <code style={{ color: 'var(--color-muted-foreground)' }}>{row.tpc_field_name}</code> → {row.tpc_field_label}
                    </div>
                  ))}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </Section>
    </div>
  )
}

function Kv({ label, value, mono }: { label: string; value: string; mono?: boolean }) {
  return (
    <div>
      <span style={{ display: 'block', fontSize: 12, color: 'var(--color-muted-foreground)' }}>{label}</span>
      <span style={{ fontSize: 14, fontWeight: 500, fontFamily: mono ? 'ui-monospace,monospace' : undefined, wordBreak: 'break-all' }}>{value}</span>
    </div>
  )
}

function Section({ title, children }: { title: string; children: ReactNode }) {
  return (
    <div style={{ ...card, overflow: 'hidden' }}>
      <div style={{ padding: '10px 14px', borderBottom: '1px solid var(--color-border)', fontSize: 13, fontWeight: 600 }}>{title}</div>
      {children}
    </div>
  )
}
