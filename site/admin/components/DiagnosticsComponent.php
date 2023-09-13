<div class="admin-panel">
    <?php // system/site Diagnostics component (for check all system/server components)
        
        // system diag card
        include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/diagnostics/SystemDiagnosticsCard.php');
        
        // website diag card
        include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/diagnostics/WebsiteDiagnosticsCard.php');
    ?><br>
</div>