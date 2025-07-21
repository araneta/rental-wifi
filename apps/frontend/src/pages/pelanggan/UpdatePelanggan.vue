<template>
  <div>
    <h2>Edit Pelanggan</h2>
    <form @submit.prevent="updatePelanggan">
      <input
        type="text"
        v-model="pelanggan.nama"
        required
        class="form-control mb-2"
      />

      <textarea
        v-model="pelanggan.alamat"
        required
        class="form-control mb-2"
      ></textarea>

      <input
        type="text"
        v-model="pelanggan.no_hp"
        required
        class="form-control mb-2"
      />

      <select
        v-model="pelanggan.paket_id"
        class="form-control mb-2"
        required
      >
        <option value="">-- Pilih Paket --</option>
        <option
          v-for="paket in paketList"
          :key="paket.id"
          :value="paket.id"
        >
          {{ paket.nama }} - Rp{{ formatHarga(paket.harga) }}
        </option>
      </select>

      <select v-model="pelanggan.status" class="form-control mb-2">
        <option value="aktif">Aktif</option>
        <option value="nonaktif">Nonaktif</option>
      </select>

      <input
        type="text"
        v-model="pelanggan.pop"
        placeholder="POP"
        class="form-control mb-2"
      />

      <button type="submit" class="btn btn-primary">Update</button>&nbsp;
      <router-link to="/pelanggans" class="btn btn-secondary">Batal</router-link>
    </form>
  </div>
</template>

<script>
import { apiFetch } from '../../api'; // or your API handler

export default {
  data() {
    return {
      pelanggan: {
        nama: '',
        alamat: '',
        no_hp: '',
        paket_id: '',
        status: 'aktif',
        pop: ''
      },
      paketList: []
    };
  },
  async mounted() {
    const id = this.$route.params.id;
    try {
      const data = await apiFetch(`/pelanggans/${id}`, { method: 'GET' });
      this.pelanggan = data.pelanggan;

      const paketData = await apiFetch('/pakets', { method: 'GET' });
      this.paketList = paketData.pakets;
    } catch (err) {
      console.error('Error fetching data:', err);
    }
  },
  methods: {
    formatHarga(harga) {
      return new Intl.NumberFormat('id-ID').format(harga);
    },
    async updatePelanggan() {
      try {
        await apiFetch(`/pelanggans/${this.pelanggan.id}`, {
          method: 'PUT',
          body: JSON.stringify(this.pelanggan)
        });
        this.$router.push('/pelanggans');
      } catch (err) {
        console.error('Update gagal', err);
      }
    }
  }
};
</script>

<style scoped>
/* Optional styling */
</style>
