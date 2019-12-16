
<!-- Preloader Start -->
<div class="preloader"><p>Loading...</p></div>
<!-- Preloader End -->

<header id="home" >
    <div class="header-top-area">
        <div class="container">
            <div class="row">

                <div class="col-sm-3">
                    <div class="logo">
                        <a href="<?php echo SITE_URL;?>index.php">WebRes</a></div>
                </div>

                <div class="col-sm-9">
                    <div class="navigation-menu">
                        <div class="navbar">

                            <div class="navbar-header">
                                <button type="button" class="navbar-toggle"
                                        data-toggle="collapse" data-target=".navbar-collapse">
                                    <span class="sr-only">Toggle navigation</span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                </button>
                            </div>

                            <div class="navbar-collapse collapse">
                                <ul class="nav navbar-nav navbar-right" >

<!--                                    <li class="active" >-->
<!--                                        <a class="smoth-scroll" href="--><?php //echo SITE_URL;?><!--index.php?page=common">-->
<!--                                            Главная <div class="ripple-wrapper"></div>-->
<!--                                        </a>-->
<!--                                    </li>-->

                                    <?php foreach ($topMenu as $key => $item) { ?>

                                        <li><a class="smoth-scroll"
                                               href="<?php echo SITE_URL;?>index.php?page=<?php echo $key;?>" >
                                               <?php echo $item['title'];?>
                                        </a></li>


<!--                                        <li><a class="smoth-scroll" href="--><?php //echo SITE_URL;?><!--index.php?page=data"       >Данные</a></li>-->
<!--                                        <li><a class="smoth-scroll" href="--><?php //echo SITE_URL;?><!--index.php?page=database"   >Базы</a></li>-->
<!--                                        <li><a class="smoth-scroll" href="--><?php //echo SITE_URL;?><!--index.php?page=scheme"     >Схема</a></li>-->
<!--                                        <li><a class="smoth-scroll" href="--><?php //echo SITE_URL;?><!--index.php?page=users"      >Пользователи</a></li>-->
<!--                                        <li><a class="smoth-scroll" href="--><?php //echo SITE_URL;?><!--index.php?page=settings"   >Настройки</a></li>-->

                                    <?php } ?>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>