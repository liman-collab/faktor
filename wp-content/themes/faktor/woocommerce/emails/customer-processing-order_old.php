<?php
/**
 * Customer processing order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-processing-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */
if (!defined('ABSPATH')) {
    exit;
}
global $wpdb;
$user_id = $order->get_user_id();
$codesTable = $wpdb->prefix . 'bex_activationcodes';
?>
<style>


    .middle-images table tr td {
        width: 20%;
        padding: 5px;
        box-sizing: border-box;
    }

    .middle-images table tr td:last-child {
        width: 40% !important;
    }

    .middle-images table tr td img {
        width: 80%;
        margin: 0 auto;
        height: auto;
    }

    .middle-images table tr td:last-child img {
        width: 100% !important;
    }
</style>
<div class="content">
    <div class="header">
        <div class="logo-1">
            <img src="<?php echo get_template_directory_uri() ?>/assets/images/faktor.png" alt="">
        </div>
        <div class="logo-2">
            <img src="<?php echo get_template_directory_uri() ?>/assets/images/edubase.jpg" alt="">
        </div>
    </div>
    <div class="clear"></div>
    <p>Sehr geehrte Dame, sehr geehrter Herr</p>
    <p>Wir freuen uns, Ihnen Ihren persönlichen Aktivierungscode für folgendes E-Book zuzustellen:</p>
    <?php
    foreach ($order->get_items() as $item):?>
        <?php
        if ($item->get_meta('Format') === 'Print') {
            continue;
        }
        ?>
        <div class="book">
            <p><b><?php echo $item->get_name() ?></b></p>
            <?php
            $product_id = $item->get_product_id();
            $row = $wpdb->get_row("select code from $codesTable where user_id = $user_id and product_id = $product_id");
            ?>
            <h1 class="codes">
                Aktivierungscode: <?php echo $row->code ?>
            </h1>
        </div>
    <?php endforeach; ?>
    <br>
    <p>
        Für den Zugang laden Sie die kostenlose edubase App aus dem iOS App Store oder dem Google Play-Store.
        Oder Sie gelangen unter <a href="http://app.edubase.ch">http://app.edubase.ch</a> über Ihren Web-Browser direkt zu Ihren E-Books.
    </p>
    <p>
        Separate Download-Manuals für die App- und Browserversionen finden Sie auch unter <a href="http://edubase.ch">http://edubase.ch</a>
    </p>

    <div class="middle-images">
        <h1 class="blue">Downloads</h1>
        <table>
            <tr>
                <td>
                    <a href="">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/images/ios-img.png" alt="">
                    </a>
                </td>
                <td>
                    <a href="">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/images/android-img.png" alt="">
                    </a>
                </td>
                <td>
                    <a href="">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/images/windows-img.png" alt="">
                    </a>
                </td>
                <td>
                    <img src="<?php echo get_template_directory_uri() ?>/assets/images/download-img.png" alt="">
                </td>
            </tr>
        </table>
    </div>

    <p>
        Dieser <span class="blue">Code kann nicht mit CTRL+C kopiert und eingefügt werden</span>! Bitte geben Sie Ihren Aktivierungscode manuell
        ein, damit Sie Zugang zu Ihren E-Books erhalten.
    </p>
    <p>
        Bei Fragen stehen wir Ihnen während der Büroöffnungszeiten sehr gerne unter +41 56 675 75 62 oder <a
                href="https://www.edubase.ch/support">https://www.edubase.ch/support</a> zur Verfügung.
    </p>
    <p>
        Viel Spass mit Ihren E-Books wünscht Ihnen
    </p>
    <p>Edubase AG</p>
    <footer>
        <table>
            <tr>
                <td>Edubase AG</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Industrie Nord 9</td>
                <td>T +41 (0)56 675 75 62</td>
                <td>
                    <a href="www.edubase.ch">www.edubase.ch</a>
                </td>
            </tr>
            <tr>
                <td>CH-5634 Merenschwand</td>
                <td>F +41 (0)56 675 75 82</td>
                <td>
                    <a href="tel:info@edubase.ch">info@edubase.ch</a>
                </td>
            </tr>
        </table>
        <p>
            Edubase AG – eine Betreibergesellschaft von Edubook AG und Careum Verlag – ist exklusiver Vertragsnehmer von LookUp! für die Schweiz.
        </p>
    </footer>
</div>
