<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bulma.min.css">
    <script src=js/script.js?<?=time() ?> defer></script>
    <title>Красочность и количество форм</title>
</head>
<body id="body" class="container py-4" style = "overflow-x:scroll">

<div class="field">
    <h1 class="title">Рассчет количества форм и красочности спусков для ролевой печати</h1>
</div>

<div class="field is-grouped">
    <div class="field mr-4">
        <label class="label">Формат</label>
        <div class="select">
            <select id="size">
                <option value = "A3">A3</option>
                <option value = "A4">A4</option>
                <option value = "A2">A2</option>
            </select>
        </div>
    </div>

    <div class="field mr-4">
        <label class="label">Красочность</label>
        <div class="select">
            <select id="default-ink">
                <option value = 1>1</option>
                <option value = 2>2</option>
                <option value = 4>4</option>
            </select>
        </div>
    </div>

    <div class="field mr-4">
        <label class="label">Количество полос</label>
        <div class="control">
            <input class="input" type="number" id="page-quantity">
        </div>
    </div>

    <div class="field mr-4">
        <label class="label">Тираж</label>
        <div class="control">
            <input class="input" type="number" id="quantity">
        </div>
    </div>

    <div class="field mr-4">
        <label class="label">Ширина роля</label>
        <div class="select">
            <select id="roll-width">
                <option value = 66>66</option>
                <option value = 70>70</option>
                <option value = 76>76</option>
                <option value = 80>80</option>
                <option value = 84>84</option>
            </select>
        </div>
    </div>
    
</div>
<table class = "table is-bordered"> 
    <tr id="pages-ink"></tr>
</table>

<div class="field">
    <label class="label"> </label>
    <button class="button is-primary">Рассчитать</button>
</div>

<table class = "table is-bordered" id="result"></table>

</body>
</html>