<?php
header('Access-Control-Allow-Origin: *');
include("includes/global.php");
include("includes/function.php");
include("manager/common_functions.php");
if(isset($_POST['ytpe']) && $_POST['type']!='chatbot')
{
include("csrf/csrf-magic.php");
}
$screen_width = testInput($_POST['screen_width']);
$temp_id = testInput($_POST['temp_id']);
$client_id = testInput($_POST['clientId']);
$sqry = "select * from sp_subdomain where client_id='" . $client_id . "'";
$resq = mysqli_query($conn,$sqry);
$domianData = mysqli_fetch_array($resq);
$subdomain_url = $domianData['subdomain_url'];
$cms = $domianData['cms_subdomain_url'];


$pmemqry1 = "SELECT * FROM sp_members where client_id='" . $client_id . "' and valid=1 and deleted=0 and approve=1";
$pmem_ftch = mysqli_query($conn,$pmemqry1);
$pc_member_info = mysqli_fetch_array($pmem_ftch);


$pcmember_pc_type = $pc_member_info['member_pc_type'];

$smemqry1 = "SELECT * FROM sp_sub_members where c_client_id= '" . $client_id . "' and valid=1 and deleted=0";
$smem_ftch = mysqli_query($conn,$smemqry1);
$row_smem = mysqli_fetch_array($smem_ftch);
$p_client_id = $row_smem['p_client_id'];






if ($pcmember_pc_type == 'C') {

    $cta_set = mysqli_query($conn,"select cta_download_url from cta_button where template_id='" . $temp_id . "' and client_id='" . $p_client_id . "' and valid=1 and deleted=0");
    $cta_get = mysqli_fetch_array($cta_set);
    $cta_download_url = $cta_get['cta_download_url'];

    $temp_set = mysqli_query($conn,"select cobrand from user_templates where content_file='" . $cta_download_url . "' and client_id='" . $p_client_id . "' and valid=1 and deleted=0");
    $temp_get = mysqli_fetch_array($temp_set);
    $cobrand = $temp_get['cobrand'];
    if ($cobrand == 1) {
        $pathPdf = 'https://' . $cms . "/upload/casestudy/$client_id/$cta_download_url";
    } else {
        $pathPdf = 'https://' . $cms . "/upload/casestudy/$p_client_id/$cta_download_url";
    }
}

if ($pcmember_pc_type == 'C') {

    $query_content_preview = mysqli_query($conn,$sd = "select template_content,mobile_content from user_templates where template_id='" . $temp_id . "' and client_id='" . $p_client_id . "' and valid=1 and deleted=0");
} else {
    $query_content_preview = mysqli_query($conn,$sd = "select template_content,mobile_content from user_templates where template_id='" . $temp_id . "' and client_id='" . $client_id . "' and valid=1 and deleted=0");
}
$row_content_preview = mysqli_fetch_array($query_content_preview);
$content_html = $row_content_preview['template_content'];
$mobile_content = $row_content_preview['mobile_content'];
if ($screen_width < 720) {
    if (!empty($mobile_content)) {
        echo $mobile_content;
    } else {
        echo $content_html;
    }
} else {
    echo $content_html;
}
?>
<script>
    var ctaConflict = jQuery.noConflict();
    ctaConflict(document).ready(function () {
        var c = "<?php echo $cobrand; ?>";
        var d = "<?php echo $pcmember_pc_type; ?>";
        var al = "<?php echo $pathPdf; ?>";
        var beetle=ctaConflict('input[name="beetle"]').val();
        ctaConflict(".hide-page").remove();
        ctaConflict(".page").css({'z-index': '', 'background': 'none', 'width': '870px;'});
        ctaConflict(".textcheck").css({'z-index': '1', 'width': '870px;'});
        ctaConflict(document).on('click', 'div[id^="beta"]', function () {
            var client_id = '<?php echo $client_id; ?>';
            var childedit_status = parseInt('<?php echo $child_edit_status; ?>');
            var a = $(this).attr('id');
            var getcta = ctaConflict("#" + a).attr("data-url");
            var b = ctaConflict("#" + a).find('a').attr("href");
            var k = b.split("/");
            if (ctaConflict.trim(getcta) == 'btnurl')
            {
                window.open(b, "_blank");
            } else
            {

                //$("#" + a).find('a').css('pointer-events','none');
                ctaConflict.ajax({
                    url: "https://<?php echo $cms; ?>/sp-ctaclick.php",
                    type: "post",
                    data: {client_id: client_id, ctaurl: k[6], childedit_status: childedit_status, pctype: d,beetle:beetle},
                    cache: false,
                    success: function (result)
                    {
                        window.open(result, "_blank");
                    }
                });

            }

        });


        var client_id = '<?php echo $client_id; ?>';
        var pc = '<?php echo $pcmember_pc_type; ?>';
        var beetle=ctaConflict('input[name="beetle"]').val();
        ctaConflict.ajax({
            url: "https://<?php echo $cms; ?>/content-childfooter.php",
            type: "post",
            data: {client_id: client_id, pc: pc,beetle:beetle},
            cache: false,
            success: function (result)
            {
                if (pc == 'C')
                {
                    ctaConflict(".child_branding").show().html(result);
                    ctaConflict("#pabout").css('display', 'none');
                    ctaConflict("#logo_img").css('display', 'none');
                } else
                {
                    ctaConflict(".child_branding").css('display', 'none');
                    ctaConflict(".footer_branding").css('display', 'none');
                }
            }
        });


    });
</script>