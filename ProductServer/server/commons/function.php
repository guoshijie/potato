<?php

function debug($data)
{
	return "<!DOCTYPE html>
            <html>
            <head>
                <title>Lumen</title>


                <style>
                    body {

                        margin: 0;
                        padding: 0;
                        width: 100%;
                        height: 100%;
                        color: #B0BEC5;
                        display: table;
                        font-weight: 100;
                        font-family: 'Lato';
                    }

                    .container {
                    	background-color: #080808;
                        text-align: center;
                        display: table-cell;
                        vertical-align: middle;
                    }

                    .content {
                        text-align: center;
                        display: inline-block;
                    }

                    .title {
                        font-size: 96px;
                        margin-bottom: 40px;
                    }

                    .quote {
                        font-size: 24px;
                    }
                </style>
            </head>
            <body>
                <div class=\"container\">
                    <div class=\"content\">
                        <div class=\"title\">".pr($data)." </div>
                    </div>
                </div>
            </body>
            </html>
        ";
}


function pr($data){
	echo "<pre><br />";
	echo json_encode($data);
	echo "<br /></pre>";
	die;
}