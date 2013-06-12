<?php

interface ITransformationObject
{
  public function __construct( array $attributes, $textValue );
  public function getMarkup();
}
