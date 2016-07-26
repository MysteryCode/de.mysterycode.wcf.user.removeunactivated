<?php

namespace wcf\system\cronjob;
use wcf\data\cronjob\Cronjob;
use wcf\data\user\UserAction;
use wcf\data\user\UserList;
use wcf\system\cronjob\AbstractCronjob;

/**
 * Removes unactivated users automatically after x days after their registration date
 * 
 * @author		Florian Gail
 * @copyright	2016 MysteryCode <https://www.mysterycode.de>
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.mysterycode.wcf.user.removeunactivated
 */
class RemoveUnactivatedUsersCronjob extends AbstractCronjob {
	/**
	 * @see	\wcf\system\cronjob\ICronjob::execute()
	 */
	public function execute(Cronjob $cronjob) {
		if (!UNACTIVATED_USERS_REMOVE)
			return;
		
		$userIDList = new UserList();
		$userIDList->getConditionBuilder()->add('user.registrationDate < ?', array(TIME_NOW - (UNACTIVATED_USERS_REMOVE_AFTER * 24 * 3600)));
		$userIDList->getConditionBuilder()->add('user.activationCode IS NOT NULL');
		$userIDList->getConditionBuilder()->add('user.activationCode <> ?)', array(0));
		$userIDList->readObjectIDs();
		$userIDs = $userIDList->getObjectIDs();
		
		if (!empty($userIDs)) {
			$userAction = new UserAction($userIDs, 'delete');
			$userAction->executeAction();
		}
	}
}
