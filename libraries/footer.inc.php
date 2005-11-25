<?php
/* $Id$ */
// vim: expandtab sw=4 ts=4 sts=4:

/**
 * WARNING: This script has to be included at the very end of your code because
 *          it will stop the script execution!
 * 
 * always use $GLOBALS, as this script is also included by functions
 * 
 */

require_once('./libraries/relation.lib.php'); // for PMA_setHistory()

/**
 * Query window
 */

// If query window is open, update with latest selected db/table.
    ?>
<script type="text/javascript">
//<![CDATA[
    <?php
    if ( ! isset( $GLOBALS['no_history'] ) && ! empty( $GLOBALS['db'] ) && empty( $GLOBALS['error_message'] ) ) {
        $table = isset( $GLOBALS['table'] ) ? $GLOBALS['table'] : '';
        // updates current settings
        ?>
    window.parent.setAll( '<?php echo $GLOBALS['lang']; ?>', '<?php echo $GLOBALS['collation_connection']; ?>', '<?php echo $GLOBALS['server']; ?>', '<?php echo $GLOBALS['db']; ?>', '<?php echo $table; ?>' );
        <?php
    }
    
    if ( ! empty( $GLOBALS['reload'] ) ) {
        ?>
    window.parent.refreshLeft();
        <?php
    }

    if ( ! isset( $GLOBALS['no_history'] ) && empty( $GLOBALS['error_message'] ) ) {
        if ( isset( $GLOBALS['LockFromUpdate'] ) && $GLOBALS['LockFromUpdate'] == '1' && isset( $GLOBALS['sql_query'] ) ) {
            // When the button 'LockFromUpdate' was selected in the querywindow,
            // it does not submit it's contents to
            // itself. So we create a SQL-history entry here.
            if ($GLOBALS['cfg']['QueryHistoryDB'] && $GLOBALS['cfgRelation']['historywork']) {
                PMA_setHistory( ( isset( $GLOBALS['db'] ) ? $GLOBALS['db'] : '' ),
                    ( isset( $GLOBALS['table'] ) ? $GLOBALS['table'] : '' ),
                    $GLOBALS['cfg']['Server']['user'],
                    $GLOBALS['sql_query'] );
            }
        }
        ?>
    window.parent.reload_querywindow(
        "<?php echo isset( $GLOBALS['db'] ) ? addslashes( $GLOBALS['db'] ) : '' ?>",
        "<?php echo isset( $GLOBALS['table'] ) ? addslashes( $GLOBALS['table'] ) : '' ?>",
        "<?php echo isset( $GLOBALS['sql_query'] ) ? urlencode( $GLOBALS['sql_query'] ) : ''; ?>" );
        <?php
    }

    if ( ! empty( $GLOBALS['focus_querywindow'] ) ) {
        ?>
    if ( parent.querywindow && !parent.querywindow.closed && parent.querywindow.location) {
        self.focus();
    }
        <?php
    }
    ?>
//]]>
</script>
    <?php

// Link to itself to replicate windows including frameset
if (!isset($GLOBALS['checked_special'])) $GLOBALS['checked_special'] = FALSE;

if (isset($_SERVER['SCRIPT_NAME']) && empty($_POST) && !$GLOBALS['checked_special']) {
    echo '<div id="selflink">' . "\n";
    echo '<a href="index.php?target=' . basename($_SERVER['SCRIPT_NAME']);
    $url = PMA_generate_common_url(isset($GLOBALS['db']) ? $GLOBALS['db'] : '', isset($GLOBALS['table']) ? $GLOBALS['table'] : '');
    if (!empty($url)) echo '&amp;' . $url;
    echo '" target="_blank">' . $GLOBALS['strDuplicateFrameset'] . '</a>' . "\n";
    echo '</div>' . "\n";
}

/**
 * Close database connections
 */
if ( isset( $GLOBALS['controllink'] ) && $GLOBALS['controllink'] ) {
    @PMA_DBI_close( $GLOBALS['controllink'] );
}
if ( isset( $GLOBALS['userlink'] ) && $GLOBALS['userlink'] ) {
    @PMA_DBI_close( $GLOBALS['userlink'] );
}

// Include possible custom footers
require_once('./libraries/footer_custom.inc.php');

/**
 * Generates profiling data if requested
 */
if ( ! empty( $GLOBALS['cfg']['DBG']['enable'] )
  && ! empty( $GLOBALS['cfg']['DBG']['profile']['enable'] ) ) {
    //run the basic setup code first
    require_once('./libraries/dbg/setup.php');
    //if the setup ran fine, then do the profiling
    if ( ! empty( $GLOBALS['DBG'] ) ) {
        require_once('./libraries/dbg/profiling.php');
        dbg_dump_profiling_results();
    }
}

?>
</body>
</html>
<?php
/**
 * Sends bufferized data
 */
if ( ! empty( $GLOBALS['cfg']['OBGzip'] )
  && ! empty( $GLOBALS['ob_mode'] ) ) {
    PMA_outBufferPost( $GLOBALS['ob_mode'] );
}

/**
 * Stops the script execution
 */
exit;
?>
