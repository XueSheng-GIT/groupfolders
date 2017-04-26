export class Api {
	getUrl (endpoint) {
		return OC.generateUrl(`apps/groupfolders/${endpoint}`);
	}

	listFolders () {
		return $.getJSON(this.getUrl('folders'));
	}

	listGroups () {
		return $.getJSON(OC.linkToOCS('cloud', 1) + 'groups?format=json')
			.then((data) => {
			console.log(data.ocs.data.groups);
				return data.ocs.data.groups;
			});
	}

	createFolder (mountPoint) {
		return $.post(this.getUrl('folders'), {
			mountpoint: mountPoint
		}).then((data) => {
			return data.id;
		});
	}

	deleteFolder (id) {
		return $.ajax({
			url: this.getUrl(`folders/${id}`),
			type: 'DELETE'
		});
	}

	addGroup (folderId, group) {
		return $.post(this.getUrl(`folders/${folderId}/groups`), {
			group
		});
	}

	removeGroup (folderId, group) {
		return $.ajax({
			url: this.getUrl(`folders/${folderId}/groups/${group}`),
			type: 'DELETE'
		});
	}
}
