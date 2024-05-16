<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <script>
        async function abrir() {
            let sAbrir = await window.arquivo.abrir();
            document.write(sAbrir.toString());
        }
        abrir();
    </script>
</body>

</html>