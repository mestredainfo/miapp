<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salvar</title>
</head>

<body>
    <script>
        async function salvar() {
            let sSalvar = await window.arquivo.salvar();
            document.write(sSalvar.toString());
        }
        salvar();
    </script>
</body>

</html>