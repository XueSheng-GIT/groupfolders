<?php
/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\GroupFolders\Trash;

use OC\Files\Storage\Wrapper\Jail;
use OCA\Files_Trashbin\Trash\ITrashBackend;
use OCA\Files_Trashbin\Trash\TrashItem;
use OCP\Files\FileInfo;
use OCP\Files\Storage\IStorage;
use OCP\IUser;

class GroupTrashItem extends TrashItem {
	private string $internalOriginalLocation;

	public function __construct(
		ITrashBackend $backend,
		string $originalLocation,
		int $deletedTime,
		string $trashPath,
		FileInfo $fileInfo,
		IUser $user,
		private string $mountPoint,
		?IUser $deletedBy,
	) {
		$this->internalOriginalLocation = $originalLocation;
		parent::__construct($backend, $this->mountPoint . '/' . $originalLocation, $deletedTime, $trashPath, $fileInfo, $user, $deletedBy);
	}

	public function getInternalOriginalLocation(): string {
		return $this->internalOriginalLocation;
	}

	public function isRootItem(): bool {
		return substr_count($this->getTrashPath(), '/') === 2;
	}

	public function getGroupFolderMountPoint(): string {
		return $this->mountPoint;
	}

	public function getTitle(): string {
		return $this->getGroupFolderMountPoint() . '/' . $this->getOriginalLocation();
	}

	public function getStorage(): IStorage {
		// get the unjailed storage, since the trash item is outside the jail
		// (the internal path is also unjailed)
		$groupFolderStorage = parent::getStorage();
		if ($groupFolderStorage->instanceOfStorage(Jail::class)) {
			/** @var Jail $groupFolderStorage */
			return $groupFolderStorage->getUnjailedStorage();
		}

		return $groupFolderStorage;
	}

	public function getMtime(): int {
		// trashbin is currently (incorrectly) assuming these to be the same
		return $this->getDeletedTime();
	}

	public function getInternalPath(): string {
		// trashbin expects the path without the deletion timestamp
		$path = parent::getInternalPath();

		return rtrim($path, '.d' . $this->getDeletedTime());
	}
}
