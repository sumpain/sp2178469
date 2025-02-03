<?php
/**
 * Result Count
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<p class="woocommerce-result-count">
	<?php
	// phpcs:disable WordPress.Security
	if ( 1 === intval( $total ) ) {
		_e( 'Zeigt das einzelne Ergebnis', 'woocommerce' );
	} elseif ( $total <= $per_page || -1 === $per_page ) {
		/* translators: %d: total results */
		printf( _n( 'Alle %d Ergebnisse werden angezeigt', 'Alle %d Ergebnisse werden angezeigt', $total, 'woocommerce' ), $total );
	} else {
		$first = ( $per_page * $current ) - $per_page + 1;
		$last  = min( $total, $per_page * $current );
		/* translators: 1: first result 2: last result 3: total results */
		printf( _nx( '%1$d&ndash;%2$d von %3$d Ergebnis werden angezeigt', '%1$d&ndash;%2$d von %3$d Ergebnissen werden angezeigt', $total, 'mit erstem und letztem Ergebnis', 'woocommerce' ), $first, $last, $total );
	}
	// phpcs:enable WordPress.Security
	?>
</p>
