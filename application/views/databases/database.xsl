<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet  [
	<!ENTITY nbsp   "&#160;">
	<!ENTITY copy   "&#169;">
	<!ENTITY reg    "&#174;">
	<!ENTITY times  "&#215;">
	<!ENTITY trade  "&#8482;">
	<!ENTITY mdash  "&#8212;">
	<!ENTITY ldquo  "&#8220;">
	<!ENTITY rdquo  "&#8221;"> 
	<!ENTITY pound  "&#163;">
	<!ENTITY yen    "&#165;">
	<!ENTITY euro   "&#8364;">
]>
<!--

 This file is part of Xerxes.

 (c) California State University <library@calstate.edu>

 For the full copyright and license information, please view the LICENSE
 file that was distributed with this source code.

-->
<!--

 Databases search page
 author: David Walker <dwalker@calstate.edu>
 
 -->
 
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl" exclude-result-prefixes="php">

<xsl:import href="../includes.xsl" />
<xsl:import href="includes.xsl" />

<xsl:output method="html" />

<xsl:template match="/*">
	<xsl:call-template name="surround" />
</xsl:template>

<xsl:template name="breadcrumb">
	<xsl:call-template name="breadcrumb_start" />
	<a href="{//request/controller}">Databases</a>
	<xsl:value-of select="$text_breadcrumb_separator" />
	<a href="{//request/controller}/alphabetical">Alphabetical</a>
	<xsl:value-of select="$text_breadcrumb_separator" />
	<xsl:text>Database</xsl:text>
</xsl:template>

<xsl:template name="module_header">

	<style type="text/css">
		
		.database-details {
			margin-top: 1em;
			max-width: 600px;
		}
		.database-link {
			margin: 2em;
		}
		.database-link a, .database-link a:visited {
			color: #fff;
		}
					
	</style>
	
</xsl:template>

<xsl:template name="module_nav">

	<xsl:call-template name="module_nav_display">
		<xsl:with-param name="url">databases-edit/database/<xsl:value-of select="databases/id" /></xsl:with-param>
	</xsl:call-template>

</xsl:template>

<xsl:template name="main">
	
	<xsl:call-template name="databases_full" />
				
</xsl:template>

<xsl:template name="databases_full">
	
	<xsl:for-each select="databases">
	
		<h1><xsl:value-of select="title" /></h1>
		
		<div class="database-record-description">
			<xsl:value-of select="description" />
		</div>
		
		<div class="database-link">
			<a href="{link}" class="btn btn-success">
				Go to <xsl:value-of select="title" />
			</a>
		</div>		
		
		<div class="database-details">
			
			<xsl:if test="coverage">
				<div>
					<dt><xsl:copy-of select="$text_database_coverage" />:</dt>
					<dd><xsl:value-of select="coverage" /></dd>
				</div>
			</xsl:if>
			
			<xsl:if test="creator">
				<div>
					<dt><xsl:copy-of select="$text_database_creator" />:</dt>
					<dd><xsl:value-of select="creator" /></dd>
				</div>
			</xsl:if>
			
			<xsl:if test="search_hints">
				<div>
					<dt><xsl:copy-of select="$text_database_search_hints" />:</dt>
					<dd><xsl:value-of select="search_hints" /></dd>
				</div>
			</xsl:if>
			
			<xsl:if test="link_guide">
				<div>
					<dt><xsl:copy-of select="$text_database_guide" />:</dt>
					<dd>
						<a href="{link_guide}">
							<xsl:value-of select="$text_database_guide_help" />
						</a>
					</dd>
				</div>
			</xsl:if>
			
		</div>
				
	</xsl:for-each>
	
</xsl:template>


</xsl:stylesheet>