(function () {
    /**
     * @namespace B24.Project.Grid
     */
    const namespace = BX.namespace('B24.Project.Grid');

    namespace.Manager = class {
        static deleteProject(gridId, projectId) {
            const grid = BX.Main.gridManager.getInstanceById(gridId);
            if (!grid) {
                throw `Grid with id ${gridId} not found`;
            }

            const confirm = new BX.UI.Dialogs.MessageBox({
                title: 'Внимание',
                message: 'Вы действительно хотите удалить проект?',
                buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
                onOk: () => {
                    confirm.close();

                    BX.ajax.runComponentAction(
                        'b24.project:project.list',
                        'deleteProject',
                        {
                            mode: 'class',
                            data: {
                                projectId: projectId
                            }
                        }
                    ).then((response) => {
                        grid.reload();
                        BX.UI.Notification.Center.notify({
                            content: 'Проект успешно удален'
                        })
                    }).catch((response) => {
                        const error = response.errors[0];
                        BX.UI.Dialogs.MessageBox.alert(
                            error.message,
                            'Ошибка'
                        );
                    });
                }
            });

            confirm.show();
        }
    }
})();