module.exports = {
  packagerConfig: {
    ignore: [
      /(.eslintrc.json)|(.gitignore)|(electron.vite.config.ts)|(forge.config.cjs)|(tsconfig.*)/,
      /^\/node_modules\/.vite/,
      /^\/.git/
    ]
  },
  makers: [
    {
      name: "@electron-forge/maker-zip",
      platforms: [
        "linux"
      ]
    }
  ],
  publishers: []
}