<?php
if (!$security_test) exit;
?>
    <section>
        <!--Spacer-->
        <div class="ptf-spacer" style=" --ptf-xxl: 5rem; --ptf-md: 1.5625rem;"></div>
        <div class="container-xxl video-background">
            <video autoplay muted loop playsinline class="background-video">
                <source src="https://servd-made-byshape.b-cdn.net/production/uploads/videos/showreel-2024-portrait_cropped.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <!--Animated Block-->
            <div class="ptf-animated-block" data-aos="fade" data-aos-delay="0">
                <!--Services List-->
                <ul class="ptf-services-list ptf-services-list--style-3" style="--ptf-font-family: var(--ptf-primary-font); max-width: 43.75rem;">
                    <?php menyu_service(1,$db_link);?>
                </ul>
            </div>
            <!--Spacer-->
            <div class="ptf-spacer" style=" --ptf-xxl: 2rem; --ptf-md: 2.8125rem;"></div>
            <!--Animated Block-->
            <div class="ptf-animated-block row" data-aos="fade" data-aos-delay="100">
                <div class="col-12 col-xl-6">
                    <?php home_content_s(11,$lang,$db_link); ?>
                </div>
                <div class="col-12 col-xl-6" style="">
                    
                </div>
            </div>
            <div class="ptf-animated-block" data-aos="fade" data-aos-delay="0"><!--Phone Block-->
                        
                    </div>
                    <div class="ptf-phone-block">
                            <div class="ptf-phone-block__icon"><i class="lnil lnil-phone"></i></div>
                            <div class="ptf-phone-block__caption">Elə indi<br>zəng edin</div>
                            <div class="ptf-phone-block__phone"><a href="tel:+994555801188">(+994) 55 580 11 88</a></div>
                        </div>
        </div>

    </section>

    <?php home_content_s(13, $lang, $db_link);?> 

    <section class="customer-logos slider">
        <?php home_photos(5, $lang, $db_link); ?>
    </section>



    <section>
        <?php
        //menyu_cont_service($db_link);
        home_news_blok_all($lang, $db_link);
        ?>
    </section>

    <section class="blackbg">
        <!--Spacer-->
        <div class="ptf-spacer" style=" --ptf-xxl: 5rem; --ptf-md: 5rem;"></div>
        <div class="container-xxl">
            <div class="d-inline-flex text-start">
                <div class="ptf-animated-block" data-aos="fade" data-aos-delay="0">
                    <?php home_content_s(15,$lang,$db_link); ?>
                </div>
            </div>
        </div>
        <div class="ptf-spacer" style=" --ptf-xxl: 5rem; --ptf-md: 5rem;"></div>
    </section>
    <section>
        <!--Spacer-->
        <div class="ptf-spacer" style=" --ptf-xxl: 5rem; --ptf-md: 5rem;"></div>
        <div class="marquee">
            <?php $contact_slug = latin_slug((string)$db_link->where('id', 3)->getValue('category', 'name_'.$lang)); ?>
            <ul class="get">
                <li><span class="text"><a href="/<?php print $lang; ?>/content/<?php print $contact_slug; ?>">Layihəniz var? Gəlin birlikdə çalışaq.</a></span></li>
             </ul>
             <ul aria-hidden="true" class="get">
                <li><span class="text"><a href="/<?php print $lang; ?>/content/<?php print $contact_slug; ?>">Layihəniz var? Gəlin birlikdə çalışaq.</a></span>
            </li>
        </ul>
        </div>
        <div class="ptf-spacer" style=" --ptf-xxl: 5rem; --ptf-md: 5rem;"></div>
    </section>
<!--     <section class="jarallax">
        <div class="ptf-spacer" style=" --ptf-xxl: 5rem; --ptf-md: 5rem;"></div>
        <div class="container-xxl">
            <div class="d-inline-flex text-start">
                <div class="ptf-animated-block" data-aos="fade" data-aos-delay="0">
                    <*?php home_content_s(3,$lang,$db_link); ?>
                </div>
            </div>
        </div>
        <div class="ptf-spacer" style=" --ptf-xxl: 5rem; --ptf-md: 5rem;"></div>
    </section> -->

