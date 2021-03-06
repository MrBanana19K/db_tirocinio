<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 15/03/18
 * Time: 17.12
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$google_user = new \auth\GoogleConnection($user); $oauth2 = $google_user->getUserProps();
$permission_manager = new \auth\PermissionManager($server, $user);

$tutti_tir = $permission_manager->check("train.readall");

// Variabili pagina
$page = "Tirocini";

?>
<html lang="it">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/head.phtml"; ?>
</head>
<body>
<?php include "../../../common/google_navbar.php"; ?>
<br>
<section class="container">
    <div class="columns">
        <aside class="column is-3 is-fullheight">
            <?php
            $index_menu = 2;
            include "../../menu.php";
            ?>
        </aside>
        <div class="column">
            <div class="box">
                <?php
                if ($tutti_tir)
                {
                    ?>
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">Filtra insegnante</label>
                        </div>
                        <div class="field-body">
                            <div class="field has-addons">
                                <div class="control is-expanded">
                                    <div class="select is-fullwidth">
                                        <select title="docente" id="filter_values">
                                            <option value="me">Solo miei</option>
                                            <option value="all" selected>Tutti</option>
                                            <optgroup label="Docente specifico">
                                                <?php
                                                $docenti = $server->prepare(
                                                    "SELECT id, nome, cognome, indirizzo_posta FROM Docente
                                                              INNER JOIN UtenteGoogle G ON Docente.utente = G.id"
                                                );

                                                $docenti->execute();
                                                $docenti->bind_result($id, $nome, $cognome, $email);
                                                while ($docenti->fetch())
                                                {
                                                    ?>
                                                    <option value="<?= $id ?>">
                                                        <?= sanitize_html($nome) ?> <?= sanitize_html($cognome) ?>
                                                        &lt;<?= sanitize_html($email) ?>&gt;
                                                    </option>
                                                    <?php
                                                }
                                                ?>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class="control">
                                    <button class="button is-info" id="filter_go">
                                        <span class="icon">
                                            <i class="fa fa-filter" aria-hidden="true"></i>
                                        </span>
                                        <span>Filtra</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Filtra studente</label>
                    </div>
                    <div class="field-body">
                        <div class="field has-addons">
                            <div class="control is-expanded">
                                <div class="select is-fullwidth">
                                    <select title="docente" id="filter_values_stu">
                                        <option value="all" selected>Tutti</option>
                                        <optgroup label="Studenti">
                                            <?php
                                            $docenti = $server->prepare(
                                                "SELECT DISTINCT G.id, nome, cognome, indirizzo_posta 
                                                              FROM Studente S
                                                              INNER JOIN Tirocinio T on S.utente = T.studente
                                                              INNER JOIN UtenteGoogle G ON S.utente = G.id
                                                            WHERE T.docenteTutore = ? OR ?"
                                            );

                                            $docenti->bind_param(
                                                "ii",
                                                $user->get_database_id(),
                                                $tutti_tir
                                            );

                                            $docenti->execute();
                                            $docenti->bind_result($id, $nome, $cognome, $email);
                                            while ($docenti->fetch())
                                            {
                                                ?>
                                                <option value="<?= $id ?>">
                                                    <?= sanitize_html($nome) ?> <?= sanitize_html($cognome) ?>
                                                    &lt;<?= sanitize_html($email) ?>&gt;
                                                </option>
                                                <?php
                                            }
                                            ?>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="control">
                                <button class="button is-info" id="filter_go_stu">
                                        <span class="icon">
                                            <i class="fa fa-filter" aria-hidden="true"></i>
                                        </span>
                                    <span>Filtra</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="tirocinis">
            </div>
            <div id="loading_go_on" data-nextid="0">
                <div class="content has-text-centered">
                    <span class="icon">
                        <i class="fa fa-circle-o-notch fa-pulse" aria-hidden="true"></i>
                    </span>
                    <span class="is-fullheight">
                        Caricamento di altri tirocini...
                    </span>
                </div>
            </div>
            <div id="loading_stop" hidden="hidden">
                <div class="content has-text-centered">
                    <span class="icon">
                        <i class="fa fa-check" aria-hidden="true"></i>
                    </span>
                    <span class="is-fullheight">
                        Non c'è più nulla da mostrare.
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>
<?php
// TODO Controllo permessi
if (true)
{
    ?>
    <script src="js/tirocini_filter.js"></script>
    <?php
}
?>
<script src="<?= BASE_DIR ?>js/tirocini_builder.js"></script>
<script>
	$ ("#controls").find ("a").removeAttr ("href");
</script>
</body>
</html>
