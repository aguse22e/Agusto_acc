$(document).ready(function() {
    function myFunction() {
        document.getElementById("report_kas").reset();
    }
    // no otomatis
    function getTotalKas() {
        var total = 0
        $(".getTotalKas").each(function() {
            // console.log($(this).val());
            var subTotalVal = parseFloat($(this).val())
            subTotalVal = isNaN(subTotalVal) ? 0 : subTotalVal
            total = total + subTotalVal
        });
        $('#total').html(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total))
    }
    $(".getTotalKas").keyup(function() {
        getTotalKas();
    });

    // tambah detail
    function addDetail(param) {
        var biggestNo = 0; //setting awal No/Id terbesar
        $(".row-detail").each(function() {
            var currentNo = parseInt($(this).attr("data-no"));
            if (currentNo > biggestNo) {
                biggestNo = currentNo;
            }
        }); //cari no terbesar

        var next = parseInt(biggestNo) + 1;
        var thisNo = param.data("no");
        var url = $("#urlAddDetail").data('url')
        console.log(url);
        $.ajax({
            type: "get",
            url: url,
            data: { biggestNo: biggestNo },
            beforeSend: function() {
                $(".loader-bg").addClass("show");
            },
            success: function(response) {
                console.log(response);
                $(".loader-bg").removeClass("show");
                $(".row-detail[data-no='" + thisNo + "']").after(response);
                $(".select2").select2();

                $(".addDetail[data-no='" + next + "']").click(function(e) {
                    e.preventDefault()
                    addDetail($(this));
                })

                $(".deleteDetail").click(function(e) {
                    e.preventDefault()
                    deleteDetail($(this));
                });
                // $(".getSubtotal").keyup(function() {
                //     getSubtotal($(this));
                // });

                // $(".getHargaSatuan").keyup(function() {
                //     // getSubtotal($(this));
                //     getHargaSatuan($(this));
                // });

                // $('.kode_barang').change(function() {
                //     kodeBarang($(this))
                // });
                $(".getTotalKas").keyup(function() {
                    getTotalKas($(this));
                });


                // $(".barang").change(function() {
                //     barang($(this));
                // });

                // $(".getTotalQty").keyup(function() {
                //     getTotalQty($(this));
                // });
                // getTotalQty();

                // $(".menu").change(function() {
                //     getDetailMenu($(this));
                // });

                // $(".menu2").change(function() {
                //     pjGetDetailMenu($(this));
                //     pjGetDiskon($(this));
                // });

                // $(".qtyPj").change(function() {
                //     getSubtotalPj($(this));
                // });
            }
        })

    }
    $(".addDetail").click(function(e) {
        e.preventDefault();
        addDetail($(this));
    });

    function deleteDetail(thisParam) {
        var delNo = thisParam.data("no");
        var parent = ".row-detail[data-no='" + delNo + "']";
        var idDetail = $(parent + " .idDetail").val();
        if (thisParam.hasClass("addDeleteId") && idDetail != 0) {
            $(".idDelete").append(
                "<input type='hidden' name='id_delete[]' value='" +
                idDetail +
                "'>"
            );
        }
        $(parent).remove();
        getTotalKas();
        // getTotalQty();
    }
    $(".deleteDetail").click(function(e) {
        e.preventDefault();
        deleteDetail($(this));
    });

})
