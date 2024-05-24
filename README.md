# MIApp

MIApp é uma plataforma que permite a criação e execução de aplicativos desenvolvidos em PHP diretamente para desktop. Compatível com Linux (Debian e Ubuntu).

## Em breve

A nova versão do MIApp está sendo desenvolvida para funcionar também no Windows 10 ou superior. Dessa forma será possível desenvolver softwares em PHP para funcionar no Linux e Windows.

Em breve disponibilizo o executável do MIApp, estou apenas realizando mais testes antes do lançamento oficial.

## Executando da Fonte

Para executar MIApp a partir do código-fonte, siga estas etapas:

- Extraia o arquivo zip
- Instale as dependencias necessárias: `npm install`
- Compile o PHP para Linux
- Crie uma pasta chamada php
- Copie o PHP compilado e a LICENSE para dentro da pasta php
- Crie um arquivo php.ini para a pasta php
- Altere o "miappserver" para "php" na variável "server" do "config/config.ini"
- Inicie o aplicativo: `npm start`

Sugestão: Caso queira disponibilizar o PHP em diferentes versões das distros Linux, recomendo criar um AppImage do PHP.

## Links

- [Página do MIApp](https://mestredainfo.wordpress.com/miapp/)
- [Apoie](https://mestredainfo.wordpress.com/apoie/)

## Licença

O MIApp é fornecido sob:

[SPDX-License-Identifier: GPL-2.0-only](https://spdx.org/licenses/GPL-2.0-only.html)

Estando sob os termos da GNU General Public License version 2 only.

Todas as contribuições para o MIApp estão sujeitas a esta licença.