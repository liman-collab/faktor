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
<div class="content" style="width: 80%;
        max-width:700px;
        margin: 0 auto;">
    <div class="header" style=" margin-bottom: 60px;">
        <div class="logo-1" style=" width: 50%;
        margin: 0;
        padding: 15px;
        box-sizing: border-box;
        float: left;
        padding-left: 0 !important;">
            <img src="<?php echo get_template_directory_uri() ?>/assets/images/faktor.png" alt="" style="
        width: 150px;
        height: auto;">
        </div>
        <div class="logo-2" style=" width: 50%;
        margin: 0;
        padding: 15px;
        box-sizing: border-box;
        float: right;
        text-align: right;">
            <img src="<?php echo get_template_directory_uri() ?>/assets/images/edubase.jpg" alt="" style="
        width: 150px;
        height: auto;">
        </div>
    </div>
    <div class="clear" style="clear: both;"></div>
    <p>Sehr geehrte Dame, sehr geehrter Herr</p>
    <p>Wir freuen uns, Ihnen Ihren persönlichen Aktivierungscode für folgendes E-Book zuzustellen:</p>
    <?php
    foreach ($order->get_items() as $item):?>
        <?php
        if ($item->get_meta('Format') === 'Print') {
            continue;
        }
        ?>
        <div class="book" style=" margin-bottom: 15px;">
            <p><b><?php echo $item->get_name() ?></b></p>
            <?php
            $product_id = $item->get_product_id();
            $row = $wpdb->get_row("select code from $codesTable where user_id = $user_id and product_id = $product_id");
            ?>
            <h1 class="codes" style="color: #00B0F0;margin-top: 0;">
                Aktivierungscode: <?php echo $row->code ?>
            </h1>
        </div>
    <?php endforeach; ?>
    <br>
    <p>
        Für den Zugang laden Sie die kostenlose edubase App aus dem iOS App Store oder dem Google Play-Store.
        Oder Sie gelangen unter <a style="color: #00B0F0;" href="http://app.edubase.ch">http://app.edubase.ch</a> über Ihren Web-Browser direkt zu Ihren E-Books.
    </p>
    <p>
        Separate Download-Manuals für die App- und Browserversionen finden Sie auch unter <a style="color: #00B0F0;" href="http://edubase.ch">http://edubase.ch</a>
    </p>

    <div class="middle-images">
        <h1 class="blue" style="color: #00B0F0;">Downloads</h1>
        <table>
            <tr>
                <td style="width: 20%;padding: 5px;box-sizing: border-box;">
                    <a style="color: #00B0F0;" href="">
                        <img style="width: 80%;margin: 0 auto;height: auto;" src="<?php echo get_template_directory_uri() ?>/assets/images/ios-img.png" alt="">
                    </a>
                </td>
                <td style="width: 20%;padding: 5px;box-sizing: border-box;">
                    <a style="color: #00B0F0;" href="">
                        <img style="width: 80%;margin: 0 auto;height: auto;" src="<?php echo get_template_directory_uri() ?>/assets/images/android-img.png" alt="">
                    </a>
                </td>
                <td style="width: 20%;padding: 5px;box-sizing: border-box;">
                    <a style="color: #00B0F0;" href="">
                        <img style="width: 80%;margin: 0 auto;height: auto;" src="<?php echo get_template_directory_uri() ?>/assets/images/windows-img.png" alt="">
                    </a>
                </td>
                <td style="width: 40%;padding: 5px;box-sizing: border-box;">
                    <img style="width: 100%;margin: 0 auto;height: auto;" src="<?php echo get_template_directory_uri() ?>/assets/images/download-img.png" alt="">
                </td>
            </tr>
        </table>
    </div>

    <p>
        Dieser <span class="blue" style="color: #00B0F0;">Code kann nicht mit CTRL+C kopiert und eingefügt werden</span>! Bitte geben Sie Ihren Aktivierungscode manuell
        ein, damit Sie Zugang zu Ihren E-Books erhalten.
    </p>
    <p>
        Bei Fragen stehen wir Ihnen während der Büroöffnungszeiten sehr gerne unter +41 56 675 75 62 oder <a style="color: #00B0F0;"
            href="https://www.edubase.ch/support">https://www.edubase.ch/support</a> zur Verfügung.
    </p>
    <p>
        Viel Spass mit Ihren E-Books wünscht Ihnen
    </p>
    <p>Edubase AG</p>
    <footer style="color: #00B0F0;margin-top: 60px;">
        <table>
            <tr>
                <td style="padding: 0 5px 5px 0;">Edubase AG</td>
                <td style="padding: 0 5px 5px 0;"></td>
                <td style="padding: 0 5px 5px 0;"></td>
            </tr>
            <tr>
                <td style="padding: 0 5px 5px 0;">Industrie Nord 9</td>
                <td style="padding: 0 5px 5px 0;">T +41 (0)56 675 75 62</td>
                <td style="padding: 0 5px 5px 0;">
                    <a style="color: #00B0F0;" href="www.edubase.ch">www.edubase.ch</a>
                </td>
            </tr>
            <tr>
                <td style="padding: 0 5px 5px 0;">CH-5634 Merenschwand</td>
                <td style="padding: 0 5px 5px 0;">F +41 (0)56 675 75 82</td>
                <td style="padding: 0 5px 5px 0;">
                    <a style="color: #00B0F0;" href="tel:info@edubase.ch">info@edubase.ch</a>
                </td>
            </tr>
        </table>
        <p>
            Edubase AG – eine Betreibergesellschaft von Edubook AG und Careum Verlag – ist exklusiver Vertragsnehmer von LookUp! für die Schweiz.
        </p>
    </footer>
</div>
