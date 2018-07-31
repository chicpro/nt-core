<?php
/**
 * Date Range Picker
 * http://www.daterangepicker.com/
 */

$html->addStyleSheet('https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css', 'header', 10);
$html->addJavaScript('https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', 'footer', 10);
$html->addJavaScript('https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', 'footer', 10);

$html->addScriptString('
<script type="text/javascript">
jQuery(function () {
    jQuery(".calendar-button").on("click", function() {
        jQuery(this).closest(".input-group").find("input").focus();
    });

    jQuery(".datepicker").daterangepicker({
        autoUpdateInput: false,
        autoApply: true,
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD"
        }
    });

    jQuery(".datepicker").on("apply.daterangepicker", function(ev, picker) {
        $(this).val(picker.startDate.format("YYYY-MM-DD"));
    });
});
</script>', 'footer', 10);