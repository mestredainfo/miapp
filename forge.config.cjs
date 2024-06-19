module.exports = {
  packagerConfig: {
    ignore: [
      /(.eslintrc.json)|(.gitignore)|(electron.vite.config.ts)|(forge.config.cjs)|(tsconfig.*)/,
      /^\/node_modules\/.vite/,
      /^\/.git/,
      /^\/.github/
    ]
  },
  makers: [
    {
      name: "@electron-forge/maker-zip",
      platforms: [
        'win32',
        "linux"
      ]
    }
  ],
  hooks: {
    postPackage: async (config, buildPath) => {
      const path = require('path')
      const fs = require('fs');

      const outputPath = buildPath.outputPaths.toString();
      const platform = buildPath.platform.toString();

      if (platform === 'win32') {
        fs.rmSync(path.join(outputPath, '/resources/app/php/linux/'), { recursive: true, force: true });
      } else if (platform === 'linux') {
        fs.rmSync(path.join(outputPath, '/resources/app/php/win32/'), { recursive: true, force: true });
      }
    }
  },
  publishers: []
}