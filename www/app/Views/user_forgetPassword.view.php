<div class="container">
    <h1>Mot de passe oublié ?</h1>
    <h4>Saisissez votre adresse mail.</h4>
    <?php
    if (isset($alert)) $this->addModal('alert', $alert);
    ?>
    <?php
    $this->addModal("form", $forgetPassword);
    ?>


</div>