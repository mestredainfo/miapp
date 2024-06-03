<?php
class milang
{
    private array $sLang = [];

    public function __construct()
    {
        //$aLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $aLang = file_get_contents(dirname(__FILE__) . '/lang/lang.txt');
        $sPath = dirname(__FILE__) . '/lang/' . $aLang . '.json';

        if (file_exists($sPath)) {
            $this->sLang = json_decode(file_get_contents($sPath), true);
        } else {
            if (file_exists(dirname(__FILE__) . '/lang/en.json')) {
                $this->sLang = json_decode(file_get_contents(dirname(__FILE__) . '/lang/en.json'), true);
            } else {
                $this->sLang = [];
            }
        }
    }

    public function traduzir(string $texto): string
    {
        return (empty($this->sLang[$texto])) ? $texto : $this->sLang[$texto];
    }
}

$milang = new milang();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Idioma</title>
</head>

<body>
    <?php
    echo $milang->traduzir('Este é um exemplo no PHP!');
    ?>
</body>

</html>