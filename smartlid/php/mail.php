<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (!empty($_POST['uname']) && (!empty($_POST['uemail']) || !empty($_POST['uphone']))){
    if (isset($_POST['uname'])) {
      $uname = strip_tags($_POST['uname']) . "<br>";
      $unameFieldset = "<b>Имя пославшего:</b>";
    }
    if (isset($_POST['uemail'])) {
      $uemail = strip_tags($_POST['uemail']) . "<br>";
      $uemailFieldset = "<b>Почта:</b>";
    }
    if (isset($_POST['uphone'])) {
      $uphone = strip_tags($_POST['uphone']) . "<br>";
      $uphoneFieldset = "<b>Телефон:</b>";
    }
    if (isset($_POST['utext'])) {
      $utext = strip_tags($_POST['utext']) . "<br>";
      $utextFieldset = "<b>Сообщение:</b>";
    }
    if (isset($_POST['formReferer'])) {
      if (!empty($_POST['formReferer'])) {
        $formReferer = strip_tags($_POST['formReferer']) . "<br>";
        $formRefererFieldset = "<b>Источник перехода на сайт:</b>";
      }
    }
    if (isset($_POST['formInfo'])) {
      $formInfo = strip_tags($_POST['formInfo']);
      $formInfoFieldset = "<b>Тема:</b>";
    }


    $to = "kurandero@yandex.ru"; /*Укажите адрес, на который должно приходить письмо*/
    $sendfrom = "kurandero@yandex.ru"; /*Укажите адрес, с которого будет приходить письмо */
    $headers  = "From: " . strip_tags($sendfrom) . "\n";
    $headers .= "Reply-To: ". strip_tags($sendfrom) . "\n";
    $headers .= "MIME-Version: 1.0\n";
    $headers .= "Content-Type: text/html;charset=utf-8 \n";
    $headers .= "Content-Transfer-Encoding: 8bit \n";
    $subject = "$formInfo";
    $message = "$unameFieldset $uname
                $uemailFieldset $uemail
                $uphoneFieldset $uphone
                $utextFieldset $utext
                $formRefererFieldset $formReferer
                $formInfoFieldset $formInfo";

    $send = mail ($to, $subject, $message, $headers);
        if ($send == 'true') {
            echo '<p class="success">Спасибо за отправку вашего сообщения!</p>';
            die();
        } else {
          echo '<p class="fail"><b>Ошибка. Сообщение не отправлено!</b></p>';
          die();
        }
  } else {
    echo '<p class="fail">Ошибка. Вы заполнили не все обязательные поля!</p>';
    die();
  }
} else {
  header ("Location: /"); // главная страница вашего лендинга
}
