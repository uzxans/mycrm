<div class="board-container">

    <div class="board" id="board">
        <div class="column" data-status="100">
            <h3 class="status_box status_work">Работает</h3>
            <button class="btn_add_user mb-3" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasRightAddNewUser" aria-controls="offcanvasRightAddNewUser">
                Добавить работника <i class="add-button-icon"></i>
            </button>
            <!--Add hrs new user-->
            <div class="offcanvas offcanvas-end hr-info" tabindex="-1" id="offcanvasRightAddNewUser"
                 aria-labelledby="offcanvasRightLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasRightLabel">Добавить нового соискателя</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                </div>
                <div class="offcanvas-body user_info_modal">
                    <div class="user_info">
                        <div class="user_info_item">
                            <div class="status_img">
                                <div class="img_box">
                                    <img src="./accets/img/hr_info_img.png" alt="">
                                </div>
                                <div class="status">
                                    <label for="exampleFormControlInput1" class="form-label">Статус</label>
                                    <select class="form-select status_new" aria-label="Default select example">
                                        <option selected>Соискатель</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                </div>
                                <div class="fio">
                                    <label for="exampleFormControlInput2" class="form-label">ФИО</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="info_body">
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Профессия</label>
                                    <input type="text" class="form-control" id="exampleFormControlInput1"
                                           placeholder="name@example.com">
                                </div>
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Телефон номер</label>
                                    <input type="text" class="form-control" id="exampleFormControlInput1"
                                           placeholder="name@example.com">
                                </div>
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Гражданство</label>
                                    <input type="text" class="form-control" id="exampleFormControlInput1"
                                           placeholder="name@example.com">
                                </div>
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Дата рождения</label>
                                    <input type="text" class="form-control" id="exampleFormControlInput1"
                                           placeholder="name@example.com">
                                </div>

                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">ИНН патента</label>
                                    <input type="text" class="form-control" id="exampleFormControlInput1"
                                           placeholder="name@example.com">
                                </div>
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Дата окончания</label>
                                    <input type="text" class="form-control" id="exampleFormControlInput1"
                                           placeholder="name@example.com">
                                </div>
                            </div>
                        </div>

                        <div class="user_info_item">
                            <div class="row align-items-center">
                                <div class="col-sm-5 col-md-4 col-xl-3">
                                    <div class="img_box">
                                        <img src="./accets/img/hr_info_img.png" alt="">
                                    </div>
                                </div>
                                <div class="col-sm-7 col-md-8 col-xl-9 d-flex align-items-center gap-1">
                                    <img src="./accets/fonts/icon/hr-icon.svg" alt="hr">
                                    <h6 class="mb-0">Приходько Виктория</h6>
                                    <!-- <div class="edit">
                                                                      <input type="text" class="form-control">
                                                                    </div> -->
                                </div>
                            </div>
                            <div class="info_body">
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Объект</label>
                                    <select class="form-select" aria-label="Default select example">
                                        <option selected>ОООПолитПром(ПМП)</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Менеджер объекта</label>
                                    <input type="text" class="form-control" id="exampleFormControlInput1"
                                           placeholder="name@example.com">
                                </div>
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Адрес</label>
                                    <input type="text" class="form-control" id="exampleFormControlInput1"
                                           placeholder="name@example.com">
                                </div>
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Метроя</label>
                                    <select class="form-select" aria-label="Default select example">
                                        <option selected>Автово</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--End user info-->

                    <div class="comment_box">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                                        aria-selected="true">Комментарий</button>
                                <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                                        type="button" role="tab" aria-controls="nav-profile"
                                        aria-selected="false">SMS/WhatsApp</button>
                                <!-- <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact"
                                                                  type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Contact</button>
                                                                <button class="nav-link" id="nav-disabled-tab" data-bs-toggle="tab" data-bs-target="#nav-disabled"
                                                                  type="button" role="tab" aria-controls="nav-disabled" aria-selected="false" disabled>Disabled</button> -->
                            </div>
                        </nav>
                        <div class="tab-content p-2" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                                 aria-labelledby="nav-home-tab" tabindex="0">
                                <div class="form position-relative">
                                    <textarea class="form-control new_comment" row="9" name="" id=""></textarea>
                                    <button class="btn btn-send m-2 position-absolute  bottom-0 end-0">Отправить</button>
                                </div>
                                <!--Comment loading-->
                                <ol class="comments_row my-5">
                                    <li class="comment_one">
                                        <div class="comment_name">Приходько Виктория</div>
                                        <div class="comment_body">
                              <textarea class="text_comment" name="" id=""
                                        row="3">запил больше обычного нет 10 дней на смене уволен 14.05.25</textarea>
                                            <div class="comment_body_footer">
                                                <div class="btn_box_comment">
                                                    <button class="btn" type="button"><img
                                                            src="./accets/fonts/icon/comment_trashcan-outline.svg" alt=""></button>
                                                    <button class="btn" type="button"><img src="./accets/fonts/icon/comment_edit.svg"
                                                                                           alt=""></button>
                                                </div>
                                                <div class="comment_date">
                                                    <img src="./accets/fonts/icon/calendar.svg" alt="">
                                                    2025-05-14
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="comment_one">
                                        <div class="comment_name">Приходько Виктория</div>
                                        <div class="comment_body">
                              <textarea class="text_comment" name="" id=""
                                        row="3">запил больше обычного нет 10 дней на смене уволен 14.05.25</textarea>
                                            <div class="comment_body_footer">
                                                <div class="btn_box_comment">
                                                    <button class="btn" type="button"><img
                                                            src="./accets/fonts/icon/comment_trashcan-outline.svg" alt=""></button>
                                                    <button class="btn" type="button"><img src="./accets/fonts/icon/comment_edit.svg"
                                                                                           alt=""></button>
                                                </div>
                                                <div class="comment_date">
                                                    <img src="./accets/fonts/icon/calendar.svg" alt="">
                                                    2025-05-14
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="comment_one">
                                        <div class="comment_name">Приходько Виктория</div>
                                        <div class="comment_body">
                              <textarea class="text_comment" name="" id=""
                                        row="3">запил больше обычного нет 10 дней на смене уволен 14.05.25</textarea>
                                            <div class="comment_body_footer">
                                                <div class="btn_box_comment">
                                                    <button class="btn" type="button"><img
                                                            src="./accets/fonts/icon/comment_trashcan-outline.svg" alt=""></button>
                                                    <button class="btn" type="button"><img src="./accets/fonts/icon/comment_edit.svg"
                                                                                           alt=""></button>
                                                </div>
                                                <div class="comment_date">
                                                    <img src="./accets/fonts/icon/calendar.svg" alt="">
                                                    2025-05-14
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="comment_one">
                                        <div class="comment_name">Приходько Виктория</div>
                                        <div class="comment_body">
                              <textarea class="text_comment" name="" id=""
                                        row="3">запил больше обычного нет 10 дней на смене уволен 14.05.25</textarea>
                                            <div class="comment_body_footer">
                                                <div class="btn_box_comment">
                                                    <button class="btn" type="button"><img
                                                            src="./accets/fonts/icon/comment_trashcan-outline.svg" alt=""></button>
                                                    <button class="btn" type="button"><img src="./accets/fonts/icon/comment_edit.svg"
                                                                                           alt=""></button>
                                                </div>
                                                <div class="comment_date">
                                                    <img src="./accets/fonts/icon/calendar.svg" alt="">
                                                    2025-05-14
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="comment_one">
                                        <div class="comment_name">Приходько Виктория</div>
                                        <div class="comment_body">
                              <textarea class="text_comment" name="" id=""
                                        row="3">запил больше обычного нет 10 дней на смене уволен 14.05.25</textarea>
                                            <div class="comment_body_footer">
                                                <div class="btn_box_comment">
                                                    <button class="btn" type="button"><img
                                                            src="./accets/fonts/icon/comment_trashcan-outline.svg" alt=""></button>
                                                    <button class="btn" type="button"><img src="./accets/fonts/icon/comment_edit.svg"
                                                                                           alt=""></button>
                                                </div>
                                                <div class="comment_date">
                                                    <img src="./accets/fonts/icon/calendar.svg" alt="">
                                                    2025-05-14
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ol>
                                <!--End-Comment loading-->
                            </div>
                            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab"
                                 tabindex="0">...
                            </div>
                            <!-- <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab" tabindex="0">...
                                                            </div>
                                                            <div class="tab-pane fade" id="nav-disabled" role="tabpanel" aria-labelledby="nav-disabled-tab" tabindex="0">
                                                              ...</div> -->
                        </div>
                    </div>
                </div>
            </div>
            <!--End Add hrs new user-->



            <div class="cards">
                <div class="card" data-status="100" data-id="1" type="button" data-bs-toggle="offcanvas"
                     data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <div class="name" data-name="Грызун">
                        <h3><?php echo $row['candidate'];?></h3>
                        <div class="flag">РФ</div>
                    </div>
                    <div class="cart_body">
                        <li><img src="./accets/fonts/icon/data-icon.svg" alt="">Грызун</li>
                        <li data-phone="89287773050"><img src="./accets/fonts/icon/phone-icon.svg" alt="">8-928-777-30-50
                        </li>
                        <li data-object="Обухова"><img src="./accets/fonts/icon/building-icon.svg" alt="">Обухова</li>
                        <li data-hr="Timdev"><img src="./accets/fonts/icon/hr-icon.svg" alt="">
                            Timdev</li>
                        <li data-metro="Звёздная"><img src="./accets/fonts/icon/metro-icon.svg" alt="">Теst</li>
                    </div>
                    <hr class="hr-cart">
                    <div data-date="2025-09-20" class="data">2025-09-20</div>
                </div>
                <div class="card" data-status="100" data-id="1" type="button" data-bs-toggle="offcanvas"
                     data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <div class="name" data-name="Грызун">
                        <h3>Грызун1</h3>
                        <div class="flag">РФ</div>
                    </div>
                    <div class="cart_body">
                        <li><img src="./accets/fonts/icon/data-icon.svg" alt="">Грызун</li>
                        <li data-phone="89287773050"><img src="./accets/fonts/icon/phone-icon.svg" alt="">8-928-777-30-50
                        </li>
                        <li data-object="Обухова"><img src="./accets/fonts/icon/building-icon.svg" alt="">Обухова</li>
                        <li data-hr="Timdev"><img src="./accets/fonts/icon/hr-icon.svg" alt="">
                            Timdev</li>
                        <li data-metro="Звёздная"><img src="./accets/fonts/icon/metro-icon.svg" alt="">Теst</li>
                    </div>
                    <hr class="hr-cart">
                    <div data-date="2025-09-20" class="data">2025-09-20</div>
                </div>
                <div class="card" data-status="100" data-id="1" type="button" data-bs-toggle="offcanvas"
                     data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <div class="name" data-name="Грызун">
                        <h3>Грызун1</h3>
                        <div class="flag">РФ</div>
                    </div>
                    <div class="cart_body">
                        <li><img src="./accets/fonts/icon/data-icon.svg" alt="">Грызун</li>
                        <li data-phone="89287773050"><img src="./accets/fonts/icon/phone-icon.svg" alt="">8-928-777-30-50
                        </li>
                        <li data-object="Обухова"><img src="./accets/fonts/icon/building-icon.svg" alt="">Обухова</li>
                        <li data-hr="Timdev"><img src="./accets/fonts/icon/hr-icon.svg" alt="">
                            Timdev</li>
                        <li data-metro="Звёздная"><img src="./accets/fonts/icon/metro-icon.svg" alt="">Теst</li>
                    </div>
                    <hr class="hr-cart">
                    <div data-date="2025-09-20" class="data">2025-09-20</div>
                </div>
                <div class="card" data-status="100" data-id="1" type="button" data-bs-toggle="offcanvas"
                     data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <div class="name" data-name="Грызун">
                        <h3>Грызун1</h3>
                        <div class="flag">РФ</div>
                    </div>
                    <div class="cart_body">
                        <li><img src="./accets/fonts/icon/data-icon.svg" alt="">Грызун</li>
                        <li data-phone="89287773050"><img src="./accets/fonts/icon/phone-icon.svg" alt="">8-928-777-30-50
                        </li>
                        <li data-object="Обухова"><img src="./accets/fonts/icon/building-icon.svg" alt="">Обухова</li>
                        <li data-hr="Timdev"><img src="./accets/fonts/icon/hr-icon.svg" alt="">
                            Timdev</li>
                        <li data-metro="Звёздная"><img src="./accets/fonts/icon/metro-icon.svg" alt="">Теst</li>
                    </div>
                    <hr class="hr-cart">
                    <div data-date="2025-09-20" class="data">2025-09-20</div>
                </div>
            </div>
        </div>
        <div class="column" data-status="1">
            <h3 class="status_new">Соискатель</h3>
            <button class="btn_add_user mb-3">Добавить работника <i class="add-button-icon"></i></button>
            <div class="cards">
                <div class="card" data-status="100" data-id="1" type="button" data-bs-toggle="offcanvas"
                     data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <div class="name" data-name="Грызун">
                        <h3>Грызун1</h3>
                        <div class="flag">РФ</div>
                    </div>
                    <div class="cart_body">
                        <li><img src="./accets/fonts/icon/data-icon.svg" alt="">Грызун</li>
                        <li data-phone="89287773050"><img src="./accets/fonts/icon/phone-icon.svg" alt="">8-928-777-30-50
                        </li>
                        <li data-object="Обухова"><img src="./accets/fonts/icon/building-icon.svg" alt="">Обухова</li>
                        <li data-hr="Timdev"><img src="./accets/fonts/icon/hr-icon.svg" alt="">
                            Timdev</li>
                        <li data-metro="Звёздная"><img src="./accets/fonts/icon/metro-icon.svg" alt="">Теst</li>
                    </div>
                    <hr class="hr-cart">
                    <div data-date="2025-09-20" class="data">2025-09-20</div>
                </div>
            </div>
        </div>
        <div class="column" data-status="7">
            <h3 class="status_box status_rezerv">Резерв</h3>
            <button class="btn_add_user mb-3">Добавить работника <i class="add-button-icon"></i></button>
            <div class="cards">
                <div class="card" data-status="100" data-id="7" type="button" data-bs-toggle="offcanvas"
                     data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <div class="name" data-name="Грызун">
                        <h3>Грызун1</h3>
                        <div class="flag">РФ</div>
                    </div>
                    <div class="cart_body">
                        <li><img src="./accets/fonts/icon/data-icon.svg" alt="">Грызун</li>
                        <li data-phone="89287773050"><img src="./accets/fonts/icon/phone-icon.svg" alt="">8-928-777-30-50
                        </li>
                        <li data-object="Обухова"><img src="./accets/fonts/icon/building-icon.svg" alt="">Обухова</li>
                        <li data-hr="Timdev"><img src="./accets/fonts/icon/hr-icon.svg" alt="">
                            Timdev</li>
                        <li data-metro="Звёздная"><img src="./accets/fonts/icon/metro-icon.svg" alt="">Теst</li>
                    </div>
                    <hr class="hr-cart">
                    <div data-date="2025-09-20" class="data">2025-09-20</div>
                </div>
            </div>
        </div>
        <div class="column" data-status="-100">
            <h3 class="status_fired">Уволен</h3>
            <button class="btn_add_user mb-3">Добавить работника <i class="add-button-icon"></i></button>
            <div class="cards">
                <div class="card" data-status="-100" data-id="1" type="button" data-bs-toggle="offcanvas"
                     data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <div class="name" data-name="Грызун">
                        <h3>Грызун1</h3>
                        <div class="flag">РФ</div>
                    </div>
                    <div class="cart_body">
                        <li><img src="./accets/fonts/icon/data-icon.svg" alt="">Грызун</li>
                        <li data-phone="89287773050"><img src="./accets/fonts/icon/phone-icon.svg" alt="">8-928-777-30-50
                        </li>
                        <li data-object="Обухова"><img src="./accets/fonts/icon/building-icon.svg" alt="">Обухова</li>
                        <li data-hr="Timdev"><img src="./accets/fonts/icon/hr-icon.svg" alt="">
                            Timdev</li>
                        <li data-metro="Звёздная"><img src="./accets/fonts/icon/metro-icon.svg" alt="">Теst</li>
                    </div>
                    <hr class="hr-cart">
                    <div data-date="2025-09-20" class="data">2025-09-20</div>
                </div>
            </div>
        </div>
        <div class="column" data-status="5">
            <h3 class="status_remind">Напомнить</h3>
            <button class="btn_add_user mb-3">Добавить работника <i class="add-button-icon"></i></button>
            <div class="cards">
                <div class="card" data-status="5" data-id="" type="button" data-bs-toggle="offcanvas"
                     data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <div class="name" data-name="Грызун">
                        <h3>Грызун1</h3>
                        <div class="flag">РФ</div>
                    </div>
                    <div class="cart_body">
                        <li><img src="./accets/fonts/icon/data-icon.svg" alt="">Грызун</li>
                        <li data-phone="89287773050"><img src="./accets/fonts/icon/phone-icon.svg" alt="">8-928-777-30-50
                        </li>
                        <li data-object="Обухова"><img src="./accets/fonts/icon/building-icon.svg" alt="">Обухова</li>
                        <li data-hr="Timdev"><img src="./accets/fonts/icon/hr-icon.svg" alt="">
                            Timdev</li>
                        <li data-metro="Звёздная"><img src="./accets/fonts/icon/metro-icon.svg" alt="">Теst</li>
                    </div>
                    <hr class="hr-cart">
                    <div data-date="2025-09-20" class="data">2025-09-20</div>
                </div>
            </div>
        </div>

        <div class="column" data-status="-1">
            <h3 class="status_refusal">Отказ</h3>
            <button class="btn_add_user mb-3">Добавить работника <i class="add-button-icon"></i></button>
            <div class="cards">
                <div class="card" data-status="-1" data-id="6" type="button" data-bs-toggle="offcanvas"
                     data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <div class="name" data-name="Грызун">
                        <h3>Грызун1</h3>
                        <div class="flag">РФ</div>
                    </div>
                    <div class="cart_body">
                        <li><img src="./accets/fonts/icon/data-icon.svg" alt="">Грызун</li>
                        <li data-phone="89287773050"><img src="./accets/fonts/icon/phone-icon.svg" alt="">8-928-777-30-50
                        </li>
                        <li data-object="Обухова"><img src="./accets/fonts/icon/building-icon.svg" alt="">Обухова</li>
                        <li data-hr="Timdev"><img src="./accets/fonts/icon/hr-icon.svg" alt="">
                            Timdev</li>
                        <li data-metro="Звёздная"><img src="./accets/fonts/icon/metro-icon.svg" alt="">Теst</li>
                    </div>
                    <hr class="hr-cart">
                    <div data-date="2025-09-20" class="data">2025-09-20</div>
                </div>
            </div>
        </div>

        <div class="column" data-status="4">
            <h3 class="status_refusal">Отказ222</h3>
            <button class="btn_add_user mb-3">Добавить работника <i class="add-button-icon"></i></button>
            <div class="cards">
                <div class="card" data-status="4" data-id="7" type="button" data-bs-toggle="offcanvas"
                     data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <div class="name" data-name="Грызун">
                        <h3>Грызун1</h3>
                        <div class="flag">РФ</div>
                    </div>
                    <div class="cart_body">
                        <li><img src="./accets/fonts/icon/data-icon.svg" alt="">Грызун</li>
                        <li data-phone="89287773050"><img src="./accets/fonts/icon/phone-icon.svg" alt="">8-928-777-30-50
                        </li>
                        <li data-object="Обухова"><img src="./accets/fonts/icon/building-icon.svg" alt="">Обухова</li>
                        <li data-hr="Timdev"><img src="./accets/fonts/icon/hr-icon.svg" alt="">
                            Timdev</li>
                        <li data-metro="Звёздная"><img src="./accets/fonts/icon/metro-icon.svg" alt="">Теst</li>
                    </div>
                    <hr class="hr-cart">
                    <div data-date="2025-09-20" class="data">2025-09-20</div>
                </div>
                <div class="card" data-status="4" data-id="7" type="button" data-bs-toggle="offcanvas"
                     data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <div class="name" data-name="Грызун">
                        <h3>Грызун1</h3>
                        <div class="flag">РФ</div>
                    </div>
                    <div class="cart_body">
                        <li><img src="./accets/fonts/icon/data-icon.svg" alt="">Грызун</li>
                        <li data-phone="89287773050"><img src="./accets/fonts/icon/phone-icon.svg" alt="">8-928-777-30-50
                        </li>
                        <li data-object="Обухова"><img src="./accets/fonts/icon/building-icon.svg" alt="">Обухова</li>
                        <li data-hr="Timdev"><img src="./accets/fonts/icon/hr-icon.svg" alt="">
                            Timdev</li>
                        <li data-metro="Звёздная"><img src="./accets/fonts/icon/metro-icon.svg" alt="">Теst</li>
                    </div>
                    <hr class="hr-cart">
                    <div data-date="2025-09-20" class="data">2025-09-20</div>
                </div>
                <div class="card" data-status="4" data-id="7" type="button" data-bs-toggle="offcanvas"
                     data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <div class="name" data-name="Грызун">
                        <h3>Грызун1</h3>
                        <div class="flag">РФ</div>
                    </div>
                    <div class="cart_body">
                        <li><img src="./accets/fonts/icon/data-icon.svg" alt="">Грызун</li>
                        <li data-phone="89287773050"><img src="./accets/fonts/icon/phone-icon.svg" alt="">8-928-777-30-50
                        </li>
                        <li data-object="Обухова"><img src="./accets/fonts/icon/building-icon.svg" alt="">Обухова</li>
                        <li data-hr="Timdev"><img src="./accets/fonts/icon/hr-icon.svg" alt="">
                            Timdev</li>
                        <li data-metro="Звёздная"><img src="./accets/fonts/icon/metro-icon.svg" alt="">Теst</li>
                    </div>
                    <hr class="hr-cart">
                    <div data-date="2025-09-20" class="data">2025-09-20</div>
                </div>
            </div>
        </div>

        <div class="column" data-status="4">
            <h3 class="status_refusal">Отказ4646</h3>
            <button class="btn_add_user mb-3">Добавить работника <i class="add-button-icon"></i></button>
            <div class="cards">
                <div class="card" data-status="4" data-id="7" type="button" data-bs-toggle="offcanvas"
                     data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <div class="name" data-name="Грызун">
                        <h3>Грызун1</h3>
                        <div class="flag">РФ</div>
                    </div>
                    <div class="cart_body">
                        <li><img src="./accets/fonts/icon/data-icon.svg" alt="">Грызун</li>
                        <li data-phone="89287773050"><img src="./accets/fonts/icon/phone-icon.svg" alt="">8-928-777-30-50
                        </li>
                        <li data-object="Обухова"><img src="./accets/fonts/icon/building-icon.svg" alt="">Обухова</li>
                        <li data-hr="Timdev"><img src="./accets/fonts/icon/hr-icon.svg" alt="">
                            Timdev</li>
                        <li data-metro="Звёздная"><img src="./accets/fonts/icon/metro-icon.svg" alt="">Теst</li>
                    </div>
                    <hr class="hr-cart">
                    <div data-date="2025-09-20" class="data">2025-09-20</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Зоны автоскролла -->
    <div class="auto-scroll-zone left" id="zoneLeft"></div>
    <div class="auto-scroll-zone right" id="zoneRight"></div>

    <!-- Визуальные подсказки -->
    <div class="scroll-indicator left" id="indicatorLeft"></div>
    <div class="scroll-indicator right" id="indicatorRight"></div>
</div>