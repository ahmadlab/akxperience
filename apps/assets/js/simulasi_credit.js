Number.prototype.formatMoney = function(c, d, t){
   var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
  return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

function hitungSimulasi() {
   harga_otr = $('#harga_otr').val();
   uang_muka = $('#uang_muka').val();
   tenor     = $('#tenor').val();
   bank      = $('#bank').val();
   
   rate = getRate(bank, tenor);
   bunga    = rate[0];
   asuransi = rate[1];
   admin    = rate[2];
   provisi  = rate[3];
   
   // hitung
   otr = harga_otr.replace(/,/g,"") * 1;
   dp = (uang_muka/100) * otr;
   pokok = otr - dp;
   bunganya = ((bunga/100)*tenor) * pokok;
   total = pokok + bunganya;
   angsuran = total / (tenor * 12);
   asuransinya = (otr * (asuransi/100)) + 30000;
   provisinya = (provisi/100) * pokok;
   pertama = dp + angsuran + asuransinya + admin + provisinya;
   
   // tampilkan
   $('#h_tenor').text(tenor + ' Tahun (' + tenor*12 + ' bulan)');
   $('#h_otr').text(otr.formatMoney(0,'.',','));
   $('#h_dp').text(dp.formatMoney(0,'.',','));
   $('#h_pokok').text(pokok.formatMoney(0,'.',','));
   $('#h_bunga').text(bunganya.formatMoney(0,'.',','));
   $('#h_total').text(total.formatMoney(0,'.',','));
   $('#h_angsuran').text(angsuran.formatMoney(0,'.',','));
   $('#h_asuransi').text(asuransinya.formatMoney(0,'.',','));
   $('#h_admin').text(admin.formatMoney(0,'.',','));
   $('#h_provisi').text(provisinya.formatMoney(0,'.',','));
   $('#h_pertama').text(pertama.formatMoney(0,'.',','));
   
   $('#inputnya').hide();
   $('#hasilnya').fadeIn();
}

function resetSimulasi() {
   $('#hasilnya').hide();
   $('#inputnya').fadeIn();
}

function getRate(bank,tahun) {
    bii = [{
            tahun: 1,
            bunga: 4.25,
            asuransi: 3.30,
            admin: 630000,
            provisi: 0.5
        },{
            tahun: 2,
            bunga: 4.75,
            asuransi: 6.11,
            admin: 680000,
            provisi: 1
        },{
            tahun: 3,
            bunga: 5.25,
            asuransi: 8.58,
            admin: 731000,
            provisi: 1
        },{
            tahun: 4,
            bunga: 6,
            asuransi: 10.89,
            admin: 780000,
            provisi: 1
        }];
    
    panin = [{
            tahun: 1,
            bunga: 4.65,
            asuransi: 3,
            admin: 631000,
            provisi: 0.5
        },{
            tahun: 2,
            bunga: 4.95,
            asuransi: 5.55,
            admin: 681000,
            provisi: 1
        },{
            tahun: 3,
            bunga: 5.1,
            asuransi: 7.8,
            admin: 731000,
            provisi: 1
        },{
            tahun: 4,
            bunga: 6,
            asuransi: 9.9,
            admin: 781000,
            provisi: 1
        }];
    
    mitsui = [{
            tahun: 1,
            bunga: 5,
            asuransi: 3,
            admin: 850000,
            provisi: 0
        },{
            tahun: 2,
            bunga: 6,
            asuransi: 5.55,
            admin: 850000,
            provisi: 0
        },{
            tahun: 3,
            bunga: 6.25,
            asuransi: 7.80,
            admin: 850000,
            provisi: 0
        },{
            tahun: 4,
            bunga: 7.5,
            asuransi: 9.90,
            admin: 850000,
            provisi: 0
        }
        ];
    
    adira = [{
            tahun: 1,
            bunga: 5.90,
            asuransi: 3.6,
            admin: 650000,
            provisi: 0
        },{
            tahun: 2,
            bunga: 6.40,
            asuransi: 6.40,
            admin: 750000,
            provisi: 0
        },{
            tahun: 3,
            bunga: 6.90,
            asuransi: 8.80,
            admin: 850000,
            provisi: 0
        },{
            tahun: 4,
            bunga: 7.40,
            asuransi: 10.80,
            admin: 950000,
            provisi: 0
        }
        ];
    
    // adjust with index that starting from 0 (zero)
    c = tahun -1;
    
    var thebank;
    switch(bank) {
        case 'bii'      : thebank = bii;break;
        case 'panin'    : thebank = panin;break;
        case 'mitsui'   : thebank = mitsui;break;
        case 'adira'    : thebank = adira;break;
        default         : thebank = bii;
    }
    
    return new Array(thebank[c].bunga, thebank[c].asuransi, thebank[c].admin, thebank[c].provisi);
}