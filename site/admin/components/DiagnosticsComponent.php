<div class="adminPanel">
    <?php //The system/site Diagnostics component (for check all system/server components)
        
        //include system diag card
        include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/diagnostics/SystemDiagnosticsCard.php');
        
        //include website diag card
        include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/diagnostics/WebsiteDiagnosticsCard.php');
    ?><br>
</div>