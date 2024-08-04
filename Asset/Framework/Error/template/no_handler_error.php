<?php

declare(strict_types=1);

/**
 * @var array $errorArray
 */

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php
        echo $errorArray['class']; ?> | Error Control Software</title>
    <style>
        body {
            font-family: "Roboto", arial, sans-serif;
        }

        #errorTable {
            margin: 40px auto 0;
            display: table;
            width: 800px;
            min-width: 800px;
            min-height: 200px;
            border-collapse: collapse;
        }

        td, th {
            padding: 0.4em;
            border: #ddd 1px solid;
        }

        .col1 {
            font-weight: 700;
            width: 16%;
            text-align: right;
            -webkit-font-smoothing: antialiased;
            -webkit-tap-highlight-color: rgba(0, 0, 0, .1);
        }

        .col2 {
            width: 84%;
            text-align: left;
        }

        .head {
            height: 40px;
            font-size: 24px;
            background-color: #d23d24;
            color: white;
        }

        .footer {
            height: 40px;
            font-size: 24px;
            background-color: #365D95;
            color: white;
            margin: unset;
        }

        a {
            text-decoration: none;
            color: white;
        }

        .row-1 {
            height: 60px;
            font-size: 18px;
        }

        code {
            display: block;
            height: 100%;
            font-size: 12px;
            overflow-x: auto;
            overflow-y: auto;
        }
    </style>
</head>

<body>
<div style="display: flex; align-items: center; height: 100%">
    <table id="errorTable">
        <tr>
            <th colspan="2" class="head"><?php
                echo $errorArray['class']; ?></th>
        </tr>
        <tr class="row-1">
            <td class="col1">Description:</td>
            <td class="col2">Errors have been detected, error code: <?php
                echo $errorArray['micro_time']; ?></td>
        </tr>
        <tr>
            <th colspan="2" class="footer">Please try to <a href="#" id="return">Go Back</a></th>
        </tr>
    </table>
</div>

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function () {
        function refresh() {
            document.location.href = "/";
        }

        document.getElementById("return").addEventListener("click", function () {
            refresh();
        });
    });
</script>
</body>

</html>