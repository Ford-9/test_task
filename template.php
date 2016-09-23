<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>%page_title%</title>

    <!-- Bootstrap -->
    <link href="../bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
</head>
<body>

<!--    ===Команды и результат===    -->
<div class="container">
    <div class="row">
        <div class=" col-lg-6 text-right">
            <h1><strong>%team1_title%</strong><br><small>%team1_country%</small></h1>
            <h1>%team1_goals%</h1>
        </div>
        <div class=" col-lg-6 text-left">
            <h1><strong>%team2_title%</strong><br><small>%team2_country%</small></h1>
            <h1>%team2_goals%</h1>
        </div>
    </div>

<!--    ===Список игроков===    -->
    <div class="row">
        <div class="col-lg-12 text-center">
            <h3>Список игроков</h3>
        </div>
        <div class=" col-lg-6">
            <h4>Список основных игроков</h4>
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Игрок</th>
                    <th>Время</th>
                    <th>Голы</th>
                    <th>Голевая передача</th>
                </tr>
                </thead>
                <tbody>
                    %team1_startGame_players%
                </tbody>
            </table>
            <h4>Список игроков, которые вышли на замену</h4>
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Игрок</th>
                    <th>Время</th>
                    <th>Голы</th>
                    <th>Голевая передача</th>
                </tr>
                </thead>
                <tbody>
                %team1_replace_players%
                </tbody>
            </table>
            <h4>Список запасных игроков</h4>
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Игрок</th>
                </tr>
                </thead>
                <tbody>
                %team1_spare_players%
                </tbody>
            </table>
        </div>
        <div class=" col-lg-6">
            <h4>Список основных игроков</h4>
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Игрок</th>
                    <th>Время</th>
                    <th>Голы</th>
                    <th>Голевая передача</th>
                </tr>
                </thead>
                <tbody>
                %team2_startGame_players%
                </tbody>
            </table>
            <h4>Список игроков, которые вышли на замену</h4>
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Игрок</th>
                    <th>Время</th>
                    <th>Голы</th>
                    <th>Голевая передача</th>
                </tr>
                </thead>
                <tbody>
                %team2_replace_players%
                </tbody>
            </table>
            <h4>Список запасных игроков</h4>
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Игрок</th>
                </tr>
                </thead>
                <tbody>
                %team2_spare_players%
                </tbody>
            </table>
        </div>
    </div>

    <!---    ===Краткое описание важных событий===   -->
    <div class="row">
        <div class="col-lg-12">
            <h4><strong>Краткое описание важных событий</strong></h4>
            <table class="table">
                <thead>
                <tr>
                    <th>Время</th>
                    <th>Событие</th>
                </tr>
                </thead>
                <tbody>
                %important%
                </tbody>
            </table>
        </div>
    </div>

    <!---    ===Полная история матча.===   -->
    <div class="row">
        <div class="col-lg-12">
            <h4><strong>Полная история матча.</strong></h4>
            <table class="table">
                <thead>
                <tr>
                    <th>Время</th>
                    <th>Событие</th>
                </tr>
                </thead>
                <tbody>
                    %messages%
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>