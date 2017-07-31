<?php

$FORMS = Array();

$FORMS['user_block'] = <<<END

<span class="user"><img src="/images/cms/ico_forum_user.gif" alt="Зарегистрированный пользователь" title="Зарегистрированный пользователь" />&nbsp;&nbsp;<b><span style="display: none;">%login%</span> %lname% %fname% (%login%)</b></span>

END;

$FORMS['guest_block'] = <<<END

<span class="guest"><img src="/images/cms/ico_forum_guest.gif" alt="Незарегистрированный пользователь" title="Незарегистрированный пользователь" />&nbsp;&nbsp;<b>%nickname%</b> (Гость)</span>

END;

$FORMS['user_block'] = <<<END

<span class="user"><img src="/images/cms/ico_forum_sv.gif" alt="Зарегистрированный пользователь" title="Зарегистрированный пользователь" />&nbsp;&nbsp;<b><span style="display: none;">%login%</span> %lname% %fname% (%login%)</b></span>

END;


?>