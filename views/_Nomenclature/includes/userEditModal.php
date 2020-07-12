<?php
use Views\_Globals\Models\User;
try {
        $isAdmin = User::permission('admin');
        $isModer = User::permission('moder');
        $usersEditPass = User::permission('nomUsers_editPass');
        $userAccess = User::getAccess();
    } catch (\Exception $e) {
        throw new Exception("Error in User::permission" . __FILE__, 1);
    }
?>
<div class="modal fade" id="userEditModal" tabindex="-1" role="dialog" aria-labelledby="userEditModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="userEditModalLabel">Редактировать данные пользователя</h4>
            </div>
            <div class="modal-body">
                <div class="panel panel-default mb-0">
                        <div class="panel-heading text-bold text-center" id="userName">User Name</div>

                        <!-- List group -->
                    <form method="post" id="editUser_form" class="form-inline">
                        <div class="list-group mb-0">
                            <div class="list-group-item">
                                <div class="form-group">
                                    <label for="userFirstName">Фамилия: </label>
                                    <input type="text" class="form-control " title="Фамилия" id="userFirstName"  name="userFirstName" value="">
                                </div>
                                <div class="form-group">
                                    <label for="userSecondName">Имя: </label>
                                    <input type="text" class="form-control " title="Имя" id="userSecondName"  name="userSecondName" value="">
                                </div>
                                <div class="form-group">
                                    <label for="userThirdName">Отчество: </label>
                                    <input type="text" class="form-control " title="Имя" id="userThirdName"  name="userThirdName" value="">
                                </div>
                            </div>
                            
                            <div class="list-group-item">
                                <div class="form-group">
                                    <label for="userLog">Логин: </label>
                                    <input type="text" class="form-control" title="Логин" id="userLog" name="userLog" value="">
                                </div>
                                <div class="form-group">
                                    <label for="userPass">Пароль: </label>
                                    <input type="text" class="form-control" title="Логин" id="userPass" name="userPass" value="">
                                </div>
                            </div>
                            
                            <div class="list-group-item">
                                <p>
                                    <div class="btn-group" role="group" aria-label="...">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <b>Список участков:</b>
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu addWCList">
                                                <?php foreach ($workingCentersDB??[] as $wcName => $subWCenters): ?>
                                                    <?php foreach ($subWCenters as $subWCenter): ?>
                                                        <li><a class="addWC" data-wcID="<?=$subWCenter['id']?>"><?= $subWCenter['name'] . ": " . $subWCenter['descr'] ?></a></li>
                                                    <?php endforeach; ?>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </p>
                                <div class="clearfix"></div>
                                <ul class="list-group wcList"></ul>
                            </div>
                            <div class="list-group-item">
                                <label for="userMTProd">Пресет разрешений: </label>
                                <select class="form-control text-right" id="userMTProd" name="userMTProd">
                                    <option value="mt_design">Дизайнер юв. изделий</option>
                                    <option value="mt_modell">3D модельер</option>
                                    <option value="mt_modellHM">Модельер-доработчик</option>
                                    <option value="mt_oper">Оператор 3D принтера</option>
                                    <option value="mt_prod">Производство юв. изделий</option>
                                    <option value="mt_tech">Технолог юв. изделий</option>
                                    <option value="mt_pdo">ПДО</option>
                                    <?php if ( $userAccess === 1 ): ?>
                                        <option value="mt_admin">Админ</option>
                                    <?php endif;?>
                                    <?php if ( $userAccess === 122 || $userAccess === 1 ): ?>
                                        <option value="mt_moder">Модератор</option>
                                    <?php endif;?>
                                </select>
                            </div>
                            <?php if ( $userAccess === 1 ): ?>
                                <div class="list-group-item">
                                    <p>
                                        <div class="btn-group" role="group" aria-label="...">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <b>Все разрешения:</b>
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu addWCList">
                                                    <?php foreach ($allPermissions??[] as $permission): ?>
                                                        <li>
                                                            <a class="addPermission" data-permID="<?=$permission['id']?>"><?=$permission['description'];?></a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </p>
                                <div class="clearfix"></div>
                                <ul class="list-group userPermList"></ul>
                                </div>
                            <?php endif;?>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <?php if  ( User::permission('nomUsers_dell') ): ?>
                    <button type="button" class="btn btn-default pull-left deleteUser"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Удалить</button>
                <?php endif; ?>
                <button type="button" class="btn btn-primary pull-right submitUserData ml-1"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span><span>Изменить</span></button>
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal"><span class="glyphicon glyphicon-triangle-left" aria-hidden="true"></span> Отмена</button>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="userOKModal" tabindex="-1" role="dialog" aria-labelledby="userOKModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="userOKModalLabel">Редактировать пользователя</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="document.location.reload(true)"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>ОК</button>
            </div>
        </div>
    </div>
</div>


<li class="list-group-item hidden wcListProto">
    <span class="wcDescr">wc name</span>
    <input type="hidden" class="hidden"  name="wcList[]" value="">
    <button type="button" title="удалить" class="close deleteWCListItem" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</li>

<?php if ( $userAccess === 1 ): ?>
<li class="list-group-item permListItemProto">
    <span class="permDescr"></span>
    <input type="hidden" class="hidden" name="" value="">
    <button type="button" title="удалить" class="close deletePermListItem" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
</li>
<?php endif; ?>