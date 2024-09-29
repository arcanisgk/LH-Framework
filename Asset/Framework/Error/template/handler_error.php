<?php

declare(strict_types=1);

/**
 * @var string $source
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

        @import url(/assets/css/firacode/fira_code.css);

        body {
            font-family: "Roboto", arial, sans-serif;
        }

        code {

            font-family: 'Fira Code', monospace;
        }

        #errorTable {
            margin: 40px auto 0;
            display: table;
            width: 1000px;
            min-width: 1000px;
            min-height: 600px;
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

        .top {
            vertical-align: top;
        }

        a {
            text-decoration: none;
            color: white;
        }

        .row-1 {
            height: 30px;
            font-size: 18px;
        }

        .container-track {
            font-family: 'Fira Code', monospace;
            font-size: 12px;
            min-height: 30px;
            height: 150px;
            max-height: 150px;
            overflow-x: auto;
            overflow-y: auto;
        }

        .container-code {
            min-height: 30px;
            height: 275px;
            max-height: 275px;
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
            <td class="col2"><?php
                echo $errorArray['description']; ?></td>
        </tr>
        <tr class="row-1">
            <td class="col1">File:</td>
            <td class="col2"><?php
                echo $errorArray['file'] ?></td>
        </tr>
        <tr class="row-1">
            <td></td>
            <td>
                <b>Line: </b><?php
                echo $errorArray['line']; ?>
                <b>Level: </b><?php
                echo $errorArray['type']; ?>
                <b>Time: </b><?php
                echo $errorArray['micro_time']; ?>
            </td>
        </tr>
        <tr class="row-3">
            <td class="col1 top">BackTrace Log:</td>
            <td class="col2">
                <div class="container-track"><?php
                    echo $errorArray['trace_msg']; ?></div>
            </td>
        </tr>
        <tr class="row-3">
            <td class="col1 top">Related Code View</td>
            <td class="col2 container-code"><?php
                echo $source; ?></td>
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