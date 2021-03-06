<?xml version="1.0" encoding="UTF-8"?>

<!--

 This file is part of Xerxes.

 (c) California State University <library@calstate.edu>

 For the full copyright and license information, please view the LICENSE
 file that was distributed with this source code.

-->
<!--

 Email Records
 author: David Walker <dwalker@calstate.edu>
 
 -->

<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl" exclude-result-prefixes="php">
<xsl:import href="../includes.xsl" />
<xsl:import href="results.xsl" />
<xsl:output method="html" />

<xsl:template match="/*">
	<xsl:call-template name="surround">
		<xsl:with-param name="surround_template">none</xsl:with-param>
		<xsl:with-param name="sidebar">none</xsl:with-param>
	</xsl:call-template>
</xsl:template>

<xsl:template name="breadcrumb">
	<xsl:call-template name="breadcrumb_folder" />
	<xsl:call-template name="page_name" />
</xsl:template>

<xsl:template name="page_name">
	<xsl:value-of select="$text_folder_export_email_options" />
</xsl:template>

<xsl:template name="main">

	<xsl:variable name="return" select="request/return" />
	
	<div class="container">
	
		<form class="form-horizontal" name="email" method="get" action="folder/export">
			<input name="lang" type="hidden" value="{//request/lang}" />
			<input name="return" type="hidden" value="{$return}" />
			<input name="output" type="hidden" value="email" />
		
			<div class="modal inline-modal">
				<div class="modal-header">
					<h3><xsl:value-of select="$text_folder_export_email_options" /></h3>
				</div>
				<div class="modal-body">
					
					<xsl:for-each select="request/record">
						<input name="record" type="hidden" value="{text()}" />
					</xsl:for-each>
					
					<div class="control-group">
						<label class="control-label" for="email"><xsl:value-of select="$text_folder_email_address" /></label>
						<div class="controls">
							<input name="email" type="text" id="email" value="{request/session/email}" />
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label" for="subject"><xsl:value-of select="$text_folder_email_subject" /></label>
						<div class="controls">
							<input name="subject" type="text" id="subject" />
						</div>
					</div>

					<div class="control-group">
						<label class="control-label" for="notes"><xsl:value-of select="$text_folder_email_notes" /></label>
						<div class="controls">
							<textarea name="notes" style="width: 300px" id="notes"></textarea>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<a href="{$return}" class="btn"><xsl:value-of select="$text_folder_export_email_cancel" /></a>
					<input type="submit" class="btn btn-primary" name="Submit">
						<xsl:attribute name="value"><xsl:value-of select="$text_folder_export_email_send" /></xsl:attribute>
					</input>
				</div>
			</div>
		</form>
		
	</div>
	
</xsl:template>
</xsl:stylesheet>
