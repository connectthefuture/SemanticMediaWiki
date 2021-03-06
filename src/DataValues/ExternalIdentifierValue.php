<?php

namespace SMW\DataValues;

use SMWStringValue as StringValue;
use SMW\ApplicationFactory;
use SMW\DIProperty;

/**
 * @private
 *
 * @license GNU GPL v2+
 * @since 2.5
 *
 * @author mwjames
 */
class ExternalIdentifierValue extends StringValue {

	/**
	 * DV identifier
	 */
	const TYPE_ID = '_eid';

	/**
	 * @var string|null
	 */
	private $substitutedUri = null;

	/**
	 * @param string $typeid
	 */
	public function __construct( $typeid = '' ) {
		parent::__construct( self::TYPE_ID );
	}

	/**
	 * @see DataValue::parseUserValue
	 *
	 * @param string $value
	 */
	protected function parseUserValue( $value ) {
		parent::parseUserValue( $value );
	}

	/**
	 * @see DataValue::getShortWikiText
	 *
	 * @param string $value
	 */
	public function getShortWikiText( $linker = null ) {

		if ( !$this->isValid() ) {
			return '';
		}

		if ( !$this->m_caption ) {
			$this->m_caption = $this->m_dataitem->getString();
		}

		if ( $linker === null ) {
			return $this->m_caption;
		}

		$substitutedUri = $this->getUriWithPlaceholderSubstitution(
			$this->m_dataitem->getString()
		);

		if ( !$this->isValid() ) {
			return '';
		}

		return \Html::rawElement(
			'span',
			array(
				'class' => 'plainlinks smw-eid'
			),
			'['. $substitutedUri . ' '. $this->m_caption . ']'
		);
	}

	/**
	 * @see StringValue::getShortHTMLText
	 */
	public function getShortHTMLText( $linker = null ) {

		if ( !$this->isValid() ) {
			return '';
		}

		if ( !$this->m_caption ) {
			$this->m_caption = $this->m_dataitem->getString();
		}

		if ( $linker === null ) {
			return $this->m_caption;
		}

		$substitutedUri = $this->getUriWithPlaceholderSubstitution(
			$this->m_dataitem->getString()
		);

		if ( !$this->isValid() ) {
			return $this->m_caption;
		}

		return \Html::rawElement(
			'a',
			array(
				'href'   => $substitutedUri,
				'target' => '_blank'
			),
			$this->m_caption
		);
	}

	/**
	 * @see StringValue::getLongWikiText
	 */
	public function getLongWikiText( $linked = null ) {
		return $this->getShortWikiText( $linked );
	}

	/**
	 * @see StringValue::getLongHTMLText
	 */
	public function getLongHTMLText( $linker = null ) {
		return $this->getShortHTMLText( $linker );
	}

	/**
	 * @since 2.5
	 *
	 * @return DataItem
	 */
	public function getUri() {

		if ( !$this->isValid() ) {
			return '';
		}

		$dataValue = $this->dataValueServiceFactory->getDataValueFactory()->newDataValueByType(
			'_uri',
			$this->getUriWithPlaceholderSubstitution( $this->m_dataitem->getString() )
		);

		return $dataValue->getDataItem();
	}

	private function getUriWithPlaceholderSubstitution( $value ) {

		if ( $this->substitutedUri !== null ) {
			return $this->substitutedUri;
		}

		$dataItem = $this->dataValueServiceFactory->getPropertySpecificationLookup()->getExternalFormatterUri(
			$this->getProperty()
		);

		if ( $dataItem === null ) {
			$this->addErrorMsg( 'smw-datavalue-external-identifier-formatter-missing' );
			return;
		}

		$dataValue = $this->dataValueServiceFactory->getDataValueFactory()->newDataValueByItem(
			$dataItem,
			new DIProperty( '_PEFU' )
		);

		if ( $dataValue->getErrors() !== array() ) {
			$this->addError( $dataValue->getErrors() );
			return;
		}

		return $this->substitutedUri = $dataValue->getUriWithPlaceholderSubstitution( $value );
	}

}
