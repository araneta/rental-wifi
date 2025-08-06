<template>
  <div>
    <h2>Tambah Pembayaran</h2>

    <div v-if="alertMessage" :class="`alert alert-${alertType}`">
      {{ alertMessage }}
    </div>

    <form @submit.prevent="submitForm">
      <div class="mb-3">
        <label for="pelanggan_id" class="form-label">Pilih Pelanggan</label>
        <select v-model="form.pelanggan_id" @change="apiFetchTagihan" class="form-control" required>
          <option value="">-- Pilih Pelanggan --</option>
          <option v-for="p in pelangganList" :key="p.id" :value="p.id">{{ p.nama }}</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="tagihan_id" class="form-label">Pilih Tagihan</label>
        <select v-model="form.tagihan_id" @change="updateJumlah" class="form-control" required placeholder="Pilih Pelanggan Terlebih Dahulu">
          
          <option v-for="t in tagihanList" :key="t.id" :value="t.id" :data-jumlah="t.jumlah">
            [{{ t.bulan_tahun }}] - Rp{{ formatNumber(t.jumlah) }}
          </option>
        </select>
      </div>

      
      <div class="mb-3">
        <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
        <select v-model="form.metode_pembayaran" class="form-control" required>
          <option value="tunai">Tunai</option>
          <option value="transfer">Transfer Bank</option>
          <option value="QRIS">QRIS</option>
          <option value="Lainnya">Lainnya</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="tanggal_pembayaran" class="form-label">Tanggal Pembayaran</label>
        <input type="date" v-model="form.tanggal_pembayaran" class="form-control" required />
      </div>

      <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
  </div>
</template>

<script>

import { apiFetch } from '../../api'


export default {
  name: 'CreatePembayaran',
  data() {
    return {
      pelangganList: [],
      tagihanList: [],
      form: {
        petugas_id: 6, // Static for now; replace with logged-in user ID in real app
        pelanggan_id: '',
        tagihan_id: '',
        jumlah: '',
        metode_pembayaran: 'tunai',
        tanggal_pembayaran: ''
      },
      alertMessage: '',
      alertType: 'success'
    }
  },
  mounted() {
    this.apiFetchPelanggan()
    const tagihanIdFromURL = this.$route.query.tagihan_id;
	if (tagihanIdFromURL) {
		this.form.tagihan_id = parseInt(tagihanIdFromURL); // or use as-is if it's a string
		this.apiFetchTagihanByID(tagihanIdFromURL);
	}
  },
  methods: {
    async apiFetchPelanggan() {
      try {
        const response = await apiFetch('/pelanggans')
        this.pelangganList = response.pelanggans || [];
        
      } catch (err) {
      console.log('err',err);
        this.alertType = 'danger'
        this.alertMessage = 'Gagal memuat daftar pelanggan.'
      }
    },
    async apiFetchTagihan() {
      this.tagihanList = []
      this.form.tagihan_id = ''
      this.form.jumlah = ''
      if (!this.form.pelanggan_id) return

      try {
        const response = await apiFetch('/tagihans/by-pelanggan/'+this.form.pelanggan_id, {
          method: 'GET',
          })
        this.tagihanList = response.tagihans || [];
      } catch (err) {
        this.alertType = 'danger'
        this.alertMessage = 'Gagal memuat tagihan.'
      }
    },
    updateJumlah() {
      const selectedTagihan = this.tagihanList.find(t => t.id === this.form.tagihan_id)
      this.form.jumlah = selectedTagihan ? selectedTagihan.jumlah : ''
    },
    async submitForm() {
		
      try {
		this.form.jumlah = parseInt(this.form.jumlah);
		this.form.pelanggan_id = parseInt(this.form.pelanggan_id);
        const res = await apiFetch('/pembayarans', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form)
        })

        this.alertType = 'success'
        this.alertMessage = 'Pembayaran berhasil ditambahkan!'
        this.resetForm();
        window.location.href='/pembayarans/';
        
      } catch (err) {
        this.alertType = 'danger'
        this.alertMessage = 'Terjadi kesalahan saat menyimpan.'
        console.error(err)
      }
    },
    resetForm() {
      this.form = {
        petugas_id: 6,
        pelanggan_id: '',
        tagihan_id: '',
        jumlah: '',
        metode_pembayaran: 'tunai',
        tanggal_pembayaran: ''
      }
      this.tagihanList = []
    },
    formatNumber(n) {
      return Number(n).toLocaleString('id-ID')
    },
    async apiFetchTagihanByID(tagihan_id) {
	  try {
		const response = await apiFetch('/tagihans/' + tagihan_id);
		if (response.tagihan) {
		  const tagihan = response.tagihan;
		  this.tagihanList = [tagihan];
		  this.form.pelanggan_id = tagihan.pelanggan_id;
		  this.form.jumlah = tagihan.jumlah;
		} else {
		  this.alertType = 'danger';
		  this.alertMessage = 'Tagihan tidak ditemukan.';
		}
	  } catch (err) {
		console.error(err);
		this.alertType = 'danger';
		this.alertMessage = 'Gagal memuat tagihan.';
	  }
	},
  }
}
</script>
