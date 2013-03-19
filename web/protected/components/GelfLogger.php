<?php
use Kisma\Core\SeedBag;

/**
 * GelfLogger.php
 *
 * This file is part of the DreamFactory Services Platform(tm) (DSP)
 * Copyright (c) 2012-2013 DreamFactory Software, Inc. <developer-support@dreamfactory.com>
 *
 * DreamFactory Services Platform(tm) <http://github.com/dreamfactorysoftware/dsp-core>
 * Copyright (c) 2012-2013 by DreamFactory Software, Inc. All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class GelfLogger extends SeedBag implements Graylog
{
	//**********************************************************************
	//* Methods
	//**********************************************************************

	/**
	 * Static method for sending a GELF message to graylog2
	 * Expects a single parameter which is a Graylog\Message or an
	 * array to pass to the Graylog\Message constructor
	 *
	 * @param array|GelfMessage $message The GELF message to log
	 *
	 * @return bool
	 */
	public static function logMessage( $message )
	{
		if ( !( $message instanceof GelfMessage ) )
		{
			$_message = new GelfMessage( $message );
			$_data = $_message->getData();
		}
		else
		{
			$_data = $message->getData();
		}

		$_toSend = self::_prepareData( $_data );

		if ( !$_toSend )
		{
			return false;
		}

		$_url = 'udp://' . self::DefaultHost . ':' . self::DefaultPort;
		$_sock = stream_socket_client( $_url );

		foreach ( $_toSend as $_buf )
		{
			if ( !fwrite( $_sock, $_buf ) )
			{
				return false;
			}
		}

		return true;
	}

	//**********************************************************************
	//* Protected Methods
	//**********************************************************************

	/**
	 * Static method for preparing GELF data to be written to UDP socket
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	protected static function _prepareData( $data )
	{
		if ( false === ( $_jsonData = json_encode( $data ) ) )
		{
			return false;
		}

		$_gzJsonData = gzcompress( $_jsonData );

		if ( $_gzJsonData === false )
		{
			return false;
		}

		if ( strlen( $_gzJsonData ) <= self::MaximumChunkSize )
		{
			return array( $_gzJsonData );
		}

		$_prepared = array();

		$_chunks = str_split( $_gzJsonData, self::MaximumChunkSize );
		$_numChunks = count( $_chunks );

		if ( $_numChunks > self::MaximumChunksAllowed )
		{
			return false;
		}

		$_msgId = hash( 'sha256', microtime( true ) . rand( 10000, 99999 ), true );
		$_seqNum = 0;

		foreach ( $_chunks as $_chunk )
		{
			$_prepared[] = self::_prepareChunk( $_chunk, $_msgId, $_seqNum, $_numChunks );
		}

		return $_prepared;
	}

	/**
	 * Static method for packing a chunk of GELF data
	 *
	 * @param string  $chunk  The chunk of gzipped JSON GELF data to prepare
	 * @param string  $msgId  The 8-byte message id, same for entire chunk set
	 * @param integer $seqNum The sequence number of the chunk (0-$seqCnt)
	 * @param integer $seqCnt The total number of chunks in the sequence
	 *
	 * @return string A packed chunk ready to write to the UDP socket
	 */
	protected static function _prepareChunk( $chunk, $msgId, $seqNum, $seqCnt )
	{
		return pack( 'CC', 30, 15 ) . $msgId . pack( 'nn', $seqNum, $seqCnt ) . $chunk;
	}
}