import {loadStripe} from '@stripe/stripe-js/pure'

export default {
	initialize: async () => {
		await loadStripe(process.env.VUE_APP_STRIPE_KEY);
	}
}