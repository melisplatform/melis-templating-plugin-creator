import { defineConfig } from 'vite'
import path from 'node:path'

/**
 * Build de la brique React MelisTemplatingPluginCreator.
 *
 * Produit un seul bundle IIFE (public/ui-react/brick.js) chargé au runtime par le shell React de
 * MelisCore quand le module est actif. React / ReactRouter sont EXTERNES, mappés sur les globals
 * de l'hôte exposés dans main.tsx de MelisCore — la brique réutilise l'instance React de l'hôte
 * (hooks, context, Router fonctionnent à travers la frontière).
 *
 * ⚠ Le build est `vite build` SEUL (pas de `tsc`) : les erreurs de type ne le font pas échouer.
 */
export default defineConfig({
  esbuild: { jsx: 'automatic' },
  build: {
    outDir: path.resolve(import.meta.dirname, '..', 'public', 'ui-react'),
    // Conserve brick.manifest.json (écrit à la main) à côté du bundle.
    emptyOutDir: false,
    lib: {
      entry: path.resolve(import.meta.dirname, 'src/brick.tsx'),
      formats: ['iife'],
      name: 'MelisTemplatingPluginCreatorBrick',
      fileName: () => 'brick.js',
    },
    rollupOptions: {
      external: ['react', 'react-dom', 'react/jsx-runtime', 'react-router-dom'],
      output: {
        globals: {
          react: 'MelisReact',
          'react-dom': 'MelisReactDOM',
          'react/jsx-runtime': 'MelisReactJsxRuntime',
          'react-router-dom': 'MelisReactRouterDOM',
        },
      },
    },
  },
})
