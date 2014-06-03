<?php
/* --------------------------------------------------------------
   hermes.lang.inc.php 2014-02-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License
   --------------------------------------------------------------
*/

$t_language_text_section_content_array = array
(
	# configuration
	'hermes_configuration' => 'Konfiguration der Hermes-Schnittstelle',
	'username' => 'Benutzername',
	'password' => 'Passwort',
	'sandbox_mode' => 'Sandbox-Mode',
	'activate_for_testing_only' => '(nur für Testbetrieb aktivieren)',
	'orderstatus_after_save' => 'Bestellstatus nach Speichern eines Versandauftrags',
	'dont_change' => 'nicht ändern',
	'orderstatus_after_label' => 'Bestellstatus nach Erzeugen eines Versandlabels',
	'save' => 'speichern',
	'configuration_saved' => 'Konfiguration gespeichert',
	'service' => 'Dienst',
	'service_props' => 'ProfiPaketService',
	'service_prips' => 'PrivatPaketService',

	# collection
	'note_props_only' => 'Diese Funktion steht nur bei Nutzung des ProfiPaketService zur Verfügung.',
	'checking_availability' => 'Verfügbarkeit der Schnittstelle wird überprüft ...',
	'collection_appointments' => 'Abhol-Aufträge',
	'new_appointment' => 'Neuer Abholauftrag',
	'constraints_info' => 'Der Termin muss folgenden Kriterien genügen:
								<ul>
									<li>Auswahl eines Werktags (Montag bis Samstag)</li>
									<li>keine Angabe eines bundesweiten, gesetzlichen Feiertags</li>
									<li>Abholung frühestens am nächsten Werktag</li>
								</ul>
								Ausnahmen:
								<ul>
									<li>Auftragserteilung nach 21:00 Uhr und einem Sendungsvolumen bis 2 Kubikmeter kann frühestens der übernächste Werktag als Abholtermin angegebenwerden
									</li>
									<li>bei Auftragserteilung nach 14:00 Uhr und einem Sendungsvolumen größer 2 Kubikmeter kann nicht garantiert werden, dass alle Pakete abgeholt werden</li>
									<li>Auftragserteilung nach 14:00 Uhr und einem Sendungsvolumen größer 18 Kubikmeter kann frühestens der übernächste Werktag als Abholtermin angegeben werden</li>
									<li>Auswahl eines Wunschtermins innerhalb von 90 Tagen nach Auftragserteilung</li>
								</ul>
								Hinweis: Wird eine Abholung an einem regionalen Feiertag beauftragt, wird die Abholung erst am nächsten Werktag durchgeführt.',
	'collection_date' => 'Abholdatum',
	'number_of_parcels_in_class' => 'Anzahl Pakete Klasse',
	'send_appointment' => 'Auftrag senden',
	'loading_list' => 'Liste wird geladen ...',
	'really_delete' => 'Wirklich löschen?',
	'not_available' => 'nicht verfügbar',
	'date' => 'Datum',
	'period' => 'Zeitraum',
	'number_of_parcels' => 'Anzahl Pakete',
	'volume' => 'Volumen',
	'more_than_two_cub_m' => 'Mehr als 2 m³',
	'storno' => 'Storno',
	'cancel' => 'stornieren',
	'cannot_retrieve_data' => 'Daten konnten nicht abgerufen werden',
	'order_cancelled' => 'Auftrag storniert',
	'order_not_cancelled' => 'Auftrag konnte nicht storniert werden',
	'error' => 'FEHLER',
	'order_saved_w_number' => 'Auftrag gespeichert, Auftragsnummer',
	
	# info
	'your_products' => 'Ihre Produkte',
	'parcel_class' => 'Paketklasse',
	'parcel_classes' => 'Paketklassen',
	'price' => 'Preis',
	'shortest_plus_longest_side_min' => 'kürzeste + längste Seite min.',
	'shortest_plus_longest_side_max' => 'kürzeste + längste Seite max.',
	'mass_min' => 'Masse min.',
	'mass_max' => 'Masse max.',
	'country' => 'Land',
	'all_classes' => '(alle)',
	'settlement' => 'Abrechnung',
	'cod_fees' => 'Nachnahmegebühren',
	'vat' => 'Mehrwertsteuer',
	'terms_and_conditions' => 'AGB der Hermes Logistik Gruppe Deutschland GmbH',
	'packaging_guidelines' => 'Verpackungsrichtlinien',
	'to_props_portal' => 'zum Hermes-ProfiPaketService-Portal',
	'account_info' => 'Informationen zu Ihrem Hermes-Webservice-Account',
	'loading' => 'wird geladen ...',
	'product' => 'Produkt',
	'product_description' => 'Beschreibung',
	'destinations' => 'Versandziele',
	'features' => 'Merkmale',
	'yes' => 'ja',
	'no' => 'nein',
	'available_optional' => 'verfügbar/optional',
	'maximum_amount' => 'Höchstbetrag',
	'surcharges' => 'Aufschläge',

	# list
	'feature_exclusive_to_props' => 'Diese Funktion steht nur bei Nutzung des ProfiPaketService zur Verfügung.',
	'select_all' => 'alle auswählen',
	'select_none' => 'alle abwählen',
	'select_unprinted' => 'ungedruckte auswählen',
	'get_labels_for_selected_orders' => 'Labels für alle selektierten Aufträge abrufen',
	'refresh' => 'aktualisieren',
	'orderno' => 'Auftr.-Nr.',
	'barcode' => 'Barcode',
	'date_order_created' => 'Datum der Auftragserzeugung',
	'date_created' => 'Datum',
	'status' => 'Status',
	'receiver' => 'Empfänger',
	'click_for_shipment_status' => 'Klicken für Sendungsstatus',
	'retrieve_label' => 'Label abrufen',
	'position' => 'Position',
	'recorded_orders' => 'Erfasste Aufträge',
	'note_90days_max500' => 'Aufträge der letzten 90 Tage. Es werden maximal 500 Aufträge angezeigt.',
	'label_for_orderno_could_not_be_created' => 'Label für Auftrag %s konnte nicht erzeugt werden.',
	'status_could_not_be_determined' => 'Status kann nicht ermittelt werden.',
	'refreshing' => 'aktualisiere ...',
	'loading_tracking_data' => 'lade Sendungsstatus ...',
	'max_40_orders' => 'Maximal 40 Aufträge auswählbar!',

	# order
	'webservice_available' => 'Hermes Webservice ist verfügbar',
	'webservice_not_available' => 'Hermes Webservice ist NICHT verfügbar',
	'hermes_label_retrieved' => 'Hermes-Versandlabel abgerufen',
	'hermes_order_saved' => 'Hermes-Versandauftrag gespeichert',
	'order_entry' => 'Hermes-Versandauftragserfassung',
	'to_order' => 'zur Bestellung',
	'shipments_for_this_order' => 'Erfasste Sendungen zu dieser Bestellung',
	'new_order' => 'neuer Auftrag',
	'sender' => 'Absender',
	'shippertype' => 'Absenderstatus',
	'private' => 'Privatperson',
	'firstname' => 'Vorname',
	'lastname' => 'Nachname',
	'address_add' => 'Adresszusatz',
	'street' => 'Straße',
	'houseno' => 'Hausnummer',
	'postcode' => 'Postleitzahl',
	'city' => 'Stadt',
	'district' => 'Bezirk',
	'phone' => 'Telefon',
	'area_code' => 'Telefonvorwahl',
	'email' => 'E-Mail',
	'note_houseno' => 'kann auch bei Straße angegeben werden',
	'for_ireland' => 'für Irland',
	'shipment_data' => 'Sendungsdaten',
	'order_no' => 'Auftragsnummer',
	'cod_amount' => 'Nachnahmebetrag',
	'handover_mode' => 'Abgabeart',
	'handover_ps' => 'Abgabe am Paketshop, Lieferung an Empfänger',
	'handover_s2s' => 'Abgabe und Lieferung an Paketshops',
	'handover_col' => 'Abholung an Haustür, Lieferung an Empfänger',
	'parcel_shop_id' => 'Paket-Shop-ID',
	'shipment_status' => 'Sendungsstatus',
	'save_and_send' => 'Auftrag speichern + senden',
	'cancel_order' => 'Auftrag stornieren/löschen',
	'print_label' => 'Paketschein anfordern',
	'working' => 'in Arbeit ...',
	'note_mustconfirm' => 'Aufträge können nur übermittelt werden,\nwenn Sie die Haftungsbeschränkungen und\nAGB ausdrücklich bestätigen.',

	'state_not_sent' => 'nicht übertragen',
	'state_sent' => 'übertragen',
	'state_printed' => 'Paketschein erzeugt',

	'error_creating_prips_label' => 'FEHLER bei der Erzeugung eines Paketscheins (PriPS)',
	'get_label' => 'Versandetikett abrufen',
	'orderstate_not_sent' => 'Daten nicht bei Hermes erfasst',
	'orderstate_printed' => 'Versandetikett erzeugt',
	'collection_desired_date' => 'Abholtermin',
);
