<?php namespace MeowCrew\CancellationOffers\Core;

trait ServiceContainerTrait {

	public function getContainer() {
		return ServiceContainer::getInstance();
	}

}
