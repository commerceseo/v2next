<?php
/**
 * @version $Id: kat_import.class.php,v 1.4 2010/09/20 12:37:18 AB Exp $
 * @version $Revision: 1.4 $
 * @copyright Copyright (c) 2009 VARIO Software GmbH
 */

	class art_ff_import extends vario_import
	{
		function art_ff_import($exp_input)
		{
			_debug($exp_input, 'START artff_import -- Übergeben wurde der Datensatz $exp_input');

			$this -> set_exp_source ( $exp_input );

			$set_fields_array = array( 'WEBSHOP_ID' => array( create_field_info ( TABLE_VARIO_FF, 'webshop_id' ) ),
									   'ID' 		=> array( create_field_info ( TABLE_VARIO_FF, 'id'		   ) ) );
			
			// Durchlaufe alle 'FELD{0-9}+' -Werte und Mappe diese, wenn das Feld in der Datenbank existiert
			foreach( $this->exp_source as $key => $value )
			{
				if( strtolower( substr( $key, 0, 4) ) == 'feld' )
				{
					$sql = "SHOW COLUMNS FROM " . TABLE_VARIO_FF . " WHERE Field = '" . strtolower ( $key ) . "'";

					if( @mysql_num_rows ( mysql_query ( $sql ) ) )
					{
						$set_fields_array += array( strtoupper ( $key ) => array( create_field_info ( TABLE_VARIO_FF, strtolower ( $key ) ) ) );
					} else {
						_debug( strtolower ( $key ), '! Feld existiert in der Tabelle "' . TABLE_VARIO_FF . '" nicht');
					}
				}
			}

			$this -> set_fields( $set_fields_array );

			$this -> assign_field_values();

			$this -> do_SQL( array ( TABLE_VARIO_FF => $this->import[TABLE_VARIO_FF] ) );
			
			_debug('', ' ENDE artff_import --');
		}
	}
?>
