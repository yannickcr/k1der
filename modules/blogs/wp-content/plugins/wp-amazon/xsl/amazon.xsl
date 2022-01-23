<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format">
	<!--====================================================================================
	Original version by : Holten Norris ( holtennorris at yahoo.com )
	Current version maintained  by: Alan Lewis (alanlewis at gmail.com)
	Enhancements by Rich Manalang (rich.manalang at gmail.com)
	Thanks to Venu Reddy from eBay XSLT team for help with the array detection code
	Protected by CDDL open source license.  
	Transforms XML into JavaScript objects, using a JSON format.
	===================================================================================== -->
	<xsl:output method="text" encoding="UTF-8"/>
	
	<!-- Specify the name of your javascript callback function  here -->
	<xsl:variable name="callback"><xsl:text>wpa2().process</xsl:text></xsl:variable>
	
	<xsl:variable name="quot"><xsl:text>"</xsl:text></xsl:variable>
	<xsl:variable name="escquot"><xsl:text>\"</xsl:text></xsl:variable>
	<xsl:template match="*">
		<xsl:param name="recursionCnt">0</xsl:param>
		<xsl:param name="isLast">1</xsl:param>
		<xsl:param name="inArray">0</xsl:param>
		
		<!-- Generates a the path of the current node -->
		<xsl:variable name="curr-path">
			<xsl:for-each select="ancestor-or-self::*">
				<xsl:value-of select="name()" /><xsl:text>/</xsl:text>
			</xsl:for-each>
			<xsl:value-of select="." />
		</xsl:variable>
		
		<!-- Need to ignore the ImageSets node since the stylesheet doesn't support nodes with attributes and child nodes -->
		<xsl:if test="not(contains($curr-path,'ImageSets'))">
			<xsl:if test="$recursionCnt=0">
				<xsl:value-of select="$callback"/><xsl:text>({</xsl:text>
			</xsl:if>
			
			<!-- test what type of data to output  -->
			<xsl:variable name="elementDataType">
				<xsl:value-of select="number(text())"/>
			</xsl:variable>
			
			<xsl:variable name="elementData">
				<!-- TEXT ( use quotes ) -->
				<xsl:if test="string($elementDataType) ='NaN'">
					<xsl:if test="boolean(text())">
						<xsl:text>"</xsl:text>
						<xsl:call-template name="replace-string">
							<xsl:with-param name="text" select="translate(text(),'&#10;','')"/>
							<xsl:with-param name="replace" select="$quot"/>
							<xsl:with-param name="with" select="$escquot"/>
						</xsl:call-template>
						<xsl:text>"</xsl:text>
					</xsl:if>
				</xsl:if>
				<!-- NUMBER (no quotes ) -->
				<xsl:if test="string($elementDataType) !='NaN'">
					<xsl:text>"</xsl:text><xsl:value-of select="text()"/><xsl:text>"</xsl:text>
				</xsl:if>
				<!-- NULL -->
				<xsl:if test="not(*)">
					<xsl:if test="not(text())">
						<xsl:text>null</xsl:text>
					</xsl:if>
				</xsl:if>
			</xsl:variable>

			<xsl:variable name="hasRepeatElements">
				<xsl:for-each select="*">
					<xsl:if test="name() = name(preceding-sibling::*) or name() = name(following-sibling::*)">
						<xsl:text>true</xsl:text>
					</xsl:if>
				</xsl:for-each>
			</xsl:variable>

			<xsl:if test="not(count(@*) &gt; 0)">
				<xsl:text>"</xsl:text><xsl:value-of select="local-name()"/><xsl:text>":</xsl:text>
				<xsl:value-of select="$elementData"/>
			</xsl:if>

			<xsl:if test="count(@*) &gt; 0">
				<xsl:text>"</xsl:text><xsl:value-of select="local-name()"/>
				<xsl:text>":{"content":</xsl:text><xsl:value-of select="$elementData"/>
				<xsl:for-each select="@*">
					<xsl:if test="position()=1"><xsl:text>,</xsl:text></xsl:if>
					<!-- test what type of data to output  -->
					<xsl:variable name="dataType">
						<xsl:value-of select="number(.)"/>
					</xsl:variable>
					<xsl:variable name="data">
						<!-- TEXT ( use quotes ) -->
						<xsl:if test="string($dataType) ='NaN'">
							<xsl:text>"</xsl:text>
							<xsl:call-template name="replace-string">
								<xsl:with-param name="text" select="translate(current(),'&#10;','')"/>
								<xsl:with-param name="replace" select="$quot"/>
								<xsl:with-param name="with" select="$escquot"/>
							</xsl:call-template>
							<xsl:text>"</xsl:text> 
						</xsl:if>
						<!-- NUMBER (no quotes ) -->
						<xsl:if test="string($dataType) !='NaN'">
							<xsl:text>"</xsl:text><xsl:value-of select="current()"/><xsl:text>"</xsl:text>
						</xsl:if>
					</xsl:variable>
					<xsl:text>"</xsl:text><xsl:value-of select="local-name()"/><xsl:text>":</xsl:text><xsl:value-of select="$data"/>
					<xsl:if test="position() !=last()"><xsl:text>,</xsl:text></xsl:if>
				</xsl:for-each>
				<xsl:text>}</xsl:text>
			</xsl:if>

			<xsl:if test="not($hasRepeatElements = '')">
						<xsl:text>[{</xsl:text>
			</xsl:if>
			<xsl:for-each select="*">
				<xsl:if test="position()=1">
					<xsl:if test="$hasRepeatElements = ''">
						<xsl:text>{</xsl:text>
					</xsl:if>
				</xsl:if>
				<xsl:apply-templates select="current()">
					<xsl:with-param name="recursionCnt" select="$recursionCnt+1"/>
					<xsl:with-param name="isLast" select="position()=last()"/>
					<xsl:with-param name="inArray" select="not($hasRepeatElements = '')"/>
				</xsl:apply-templates>
				<xsl:if test="position()=last()">
					<xsl:if test="$hasRepeatElements = ''">
						<xsl:text>}</xsl:text>
					</xsl:if>
				</xsl:if>
			</xsl:for-each>
			<xsl:if test="not($hasRepeatElements = '')">
						<xsl:text>}]</xsl:text>
					</xsl:if>
			<xsl:if test="not( $isLast )">
				<xsl:if test="$inArray = 'true'">
					<xsl:text>}</xsl:text>
				</xsl:if>
				<xsl:text>,</xsl:text> 
				<xsl:if test="$inArray = 'true'">
					<xsl:text>{</xsl:text>
				</xsl:if>
			</xsl:if>
			<xsl:if test="$recursionCnt=0"><xsl:text>});</xsl:text></xsl:if>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="replace-string">
		<xsl:param name="text"/>
		<xsl:param name="replace"/>
		<xsl:param name="with"/>
		<xsl:choose>
			<xsl:when test="contains($text,$replace)">
				<xsl:value-of select="substring-before($text,$replace)"/>
				<xsl:value-of select="$with"/>
				<xsl:call-template name="replace-string">
					<xsl:with-param name="text" select="substring-after($text,$replace)"/>
					<xsl:with-param name="replace" select="$replace"/>
					<xsl:with-param name="with" select="$with"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$text"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>