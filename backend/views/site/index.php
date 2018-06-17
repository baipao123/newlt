<?= \layuiAdm\widgets\PanelWidget::widget([
    "panels" => $panels,
    "aHref"  => false,
]) ?>
<script>
    $(document).ready(function () {
        $(document).on("click", ".panel a", function () {
            var title = $(this).find("cite").text(),
                href = $(this).attr("data-url");
            parent.globalBodyTab.tabAddiFrame(title, "", href);
        })
    });
</script>
