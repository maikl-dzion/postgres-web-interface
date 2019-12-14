<?php
  include 'bootstrap.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="copyright" content="">
    <meta name="robots" content="index,follow">
    <title>Управление базой данных</title>

    <?php include HEAD;?>

    <script>
        const apiUrl = 'http://bolderfest.ru/API_DB_CONTROL_PANEL/api.php';
    </script>
</head>

<body>

<div id="app-page" @mouseup="dragStop($event)" >


    <alert-message
        v-if="alertMessageShow"
        :message="alertMessage"
        :show="alertMessageShow"
        @alert_message_close="alertMessageClose"
    ></alert-message>

    <!-- Menu Section Start -->
    <?php include HEADER_PAGE; ?>

    <!---- ОСНОВНОЙ КОНТЕНТ -->
    <section id="about" class="about section-space-padding">
          <?php include FILE_NAME; ?>
    </section>
    <!---- </ ОСНОВНОЙ КОНТЕНТ -->

<!--    <a class="button button-style button-style-dark button-style-color-2"
          data-toggle="modal"
          data-target="#subscribemodal" href="#">Subscribe</a>-->


    <!-- FOOTER --->
    <?php include FOOTER_PAGE; ?>

</div>

<!-- All Javascript Plugins  -->
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/plugin.js"></script>
<script type="text/javascript" src="js/scripts.js"></script>

<script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vuetify/dist/vuetify.js"></script>
<script src="https://unpkg.com/vue-material"></script>
<script src="https://unpkg.com/vue-router/dist/vue-router.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue-resource@1.5.1"></script>
<script src="https://unpkg.com/http-vue-loader"></script>

<script src="app/services.js" ></script>
<script src="app/mixins.js"></script>
<script src="app/dragMixin.js"></script>
<script src="app/formEditComponent.js"></script>
<script src="app/components.js"></script>
<script src="app/app.js"      ></script>

</body>
</html>