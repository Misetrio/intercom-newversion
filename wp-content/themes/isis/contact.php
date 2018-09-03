<!DOCTYPE HTML>
<html>
<head>
    <!--<?php require_once("header.php"); ?>-->
<?php get_header(); ?>
</head>
<body id="pageBody">

<!--<?php require_once("main.php"); ?>-->

<div id="contentOuterSeparator"></div>

<div class="container">

    <div class="divPanel page-content">

        <div class="breadcrumbs">
                <a href="index.php">Главная</a> &nbsp;/&nbsp; <span>Контакты</span>
            </div> 

        <div class="row-fluid">
                <div class="span8" id="divMain">

                   	<h3 style="color:#FF6633;"><?php// echo $_GET[msg];?></h3>
					<img src="images/contact.jpg">
					<hr>
			<!--Start Contact form -->		                                                
<form name="enq" method="post" action="email/" onsubmit="return validation();">
  <fieldset>
    
	<input type="text" name="name" id="name" value=""  class="input-block-level" placeholder="Имя" />
    <input type="text" name="email" id="email" value="" class="input-block-level" placeholder="Email" />
    <textarea rows="11" name="message" id="message" class="input-block-level" placeholder="Комментарии"></textarea>
    <div class="actions">
	<input type="submit" value="Отправить" name="submit" id="submitButton" class="btn btn-info pull-right" title="Click here to submit your message!" />
	</div>
	
	</fieldset>
</form>  				 
			<!--End Contact form -->											 
                </div>
				
			<!--Edit Sidebar Content here-->	
                <div class="span4 sidebar">

                    <div class="sidebox">
                        <h3 class="sidebox-title">Контактная информация</h3>
                    <p>
                        <address><strong>OOO Интерком
                        <br />
                        Адрес: пр. Рудаки 81-2, Душанбе, Таджикистан<br>Почтовый индекс: 734024<br />
                        <abbr title="Phone">Тел:</abbr> (42) 780 00 99 / 221 70 95<br>
			с 9<sup>00</sup> до 24<sup>00</sup>в будни<br>
			с 9<sup>00</sup> до 14<sup>00</sup>в субботу и воскресенье
                             <br> Телефон технической поддержки :  <br> 221-34-52 / (42)780-00-22<br>
			<br>Пункт оказания услуг в Корвон<br>
			Корвон-Сити, 2-этаж<br>
			Телефон: (90) 775-77-55<br>
			Услуги подключения и приёма платежей<br>
			с 9<sup>00</sup> до 15<sup>00</sup><br>
                        </address>
                        <address>  <strong>Email:</strong>
                        <a href="mailto:manager@tjinter.com">manager@tjinter.com</a></address>
                        <address><strong> Адрес представительства по Согдийской области:</strong><br />
                            г. Худжанд, ул. К.Худжанди 162<br />
                            Телефон технической поддержки:<br>(83422) 6-00-80, (83422) 4-30-61</address>
                        <address>  <strong>WEB:</strong>
                            <a href="http://www.sugdinter.com">http://www.sugdinter.com/</a></address>

                        </address>
                    </p>     
                     
                    					
                    </div>
					
					
                    
                </div>
			<!--/End Sidebar Content-->
				
				
            </div>			

        <div id="footerInnerSeparator"></div>
    </div>

</div>

<!--<?php require_once("footer.php"); ?>-->

</body>
</html>