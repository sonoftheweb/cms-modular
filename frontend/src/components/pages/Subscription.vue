<template>
	<div>
		<con-page-header provided-title="Scalable pricing without fuss..."/>
		<div>
			<div class="margin-text">
				Our pay-per-seat model is based on two levels of users: Core Users and Collaborators.
				No implementation fees, unlimited contract storage and all features are available right out of the box
				no matter the number of seats.
			</div>
		</div>
		
		<div class="mt-5" v-if="data_ready">
			<v-card outlined width="70%">
				<v-simple-table>
					<template v-slot:default>
						<thead>
						<tr>
							<th class="text-left"></th>
							<th v-for="(plan, index) in plans" style="text-transform: capitalize" class="text-center" :key="index">{{
								plan.nickname }}
							</th>
						</tr>
						</thead>
						<tbody>
						<tr v-for="(item, index) in features" :key="index">
							<td>{{ item }}</td>
							<td v-for="(plan, index) in plans" :key="index" class="text-center">
								<v-icon class="ml-5">mdi-check</v-icon>
							</td>
						</tr>
						<tr class="grey lighten-4">
							<td></td>
							<td v-for="(plan, index) in plans" :key="index" class="text-center">
								<div class="mt-2"><span class="font-weight-thin title">{{ price(plan.tiers[0].unit_amount) }}</span> for
									first seat per {{ plan.interval }}
								</div>
								<div class="mb-2"><span
									class="font-weight-bold subtitle-1">{{ price(plan.tiers[1].unit_amount) }}</span> per subsequent seat
									per {{ plan.interval }}
								</div>
								<v-btn class="mb-2" depressed color="indigo" dark @click="planSelected(plan)">
									Subscribe to {{ plan.nickname }} plan
								</v-btn>
							</td>
						</tr>
						</tbody>
					</template>
				</v-simple-table>
			</v-card>
		</div>
		<con-payment v-if="displayPaymentForm" @close_payment_dialog="displayPaymentForm = false" :display="displayPaymentForm" :selected-plan="this.selectedPlan"/>
	</div>
</template>

<script>
	import ConPageHeader from '../utils/ConPageHeader'
	import ConPayment from '../subscription/ConPayment'

	export default {
		data() {
			return {
				data_ready: false,
				alert: true,
				features: [
					'Unlimited contracts',
					'Unlimited contract templates',
					'Reporting and metrics',
					'Team management',
					'Manage user permissions',
					'Real time signatures',
					'Emails and alerts',
					'Exports to PDF',
					'Contract to project management',
				],
				plans: null,
				selectedPlan: null,
				displayPaymentForm: false
			}
		},
		components: {
			ConPageHeader,
			ConPayment
		},
		methods: {
			price(cents) {
				const formatter = new Intl.NumberFormat('en-US', {
					style: 'currency',
					currency: 'USD',
					minimumFractionDigits: 2
				})
				return formatter.format(cents / 100)
			},
			planSelected(plan) {
				this.selectedPlan = plan
				this.displayPaymentForm = true
			}
		},
		mounted() {
			this.$http.get('/api/payment/plans').then(response => {
				this.plans = response.data
				this.data_ready = true
			})
		}
	}
</script>