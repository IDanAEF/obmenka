<?php
    if (isset($_POST['post-type']) && $_POST['post-type'] == 'abroad') {
        $to = $_POST['sender_e-addr'] ?: "e.dev@german-web.ru"; 
        $from = $_POST['sender_e-addr'] ?: "e.dev@german-web.ru";
        $subject = "Obmenka: Оформлена заявка на покупку за рубежом"; 
        $messText = "
            Ссылка на товар или счет для оплаты: ".$_POST['feedlink']."
            Название товара: ".$_POST['feedproduct']."
            Цена товара: ".$_POST['feedprice']."
            Детали заказа: ".$_POST['feedmess']."
            Способ связи: ".$_POST['feedcontact']."
        ";
        $message = "Информационное сообщение Obmenka
        ------------------------------------------
        
        Вам было отправлено сообщение через форму \"Оформление заявки на покупку за рубежом\" - 
        
        ".$messText."
        
        Сообщение сгенерировано автоматически"; 
        
        $boundary = "--".md5(uniqid(time())); 
        $mailheaders = "MIME-Version: 1.0\n"; 
        $mailheaders .="Content-Type: multipart/mixed; boundary=".$boundary."\n"; 
        $mailheaders .= "From: ".$from."\r\n";
        $multipart = "--".$boundary."\n"; 
        $multipart .= "Content-Type: text/plain; charset=UTF-8\n\n"; 
        $multipart .= $message."\n\n"; 
        
        $rsf = mail($to,$subject,$multipart,$mailheaders);
    } else if (isset($_POST['post-type']) && $_POST['post-type'] == 'pers-pay') {
        $to = $_POST['addr'] ?: "e.dev@german-web.ru"; 
        $from = $_POST['addr'] ?: "e.dev@german-web.ru";
        $subject = "Obmenka: Пользователь совершил перевод";
        $message = "Информационное сообщение Obmenka
        ------------------------------------------
        
        Пользователь совершил перевод по заявке:

        https://topobmenka.com/wp-admin/post.php?post=".$_POST['order-id']."&action=edit
        
        
        Сообщение сгенерировано автоматически"; 
        
        $boundary = "--".md5(uniqid(time())); 
        $mailheaders = "MIME-Version: 1.0\n"; 
        $mailheaders .="Content-Type: multipart/mixed; boundary=".$boundary."\n"; 
        $mailheaders .= "From: ".$from."\r\n";
        $multipart = "--".$boundary."\n"; 
        $multipart .= "Content-Type: text/plain; charset=UTF-8\n\n"; 
        $multipart .= $message."\n\n"; 
        
        $rsf = mail($to,$subject,$multipart,$mailheaders);
    }
?>