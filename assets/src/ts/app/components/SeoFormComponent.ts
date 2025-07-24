import { agentService } from '../services/AgentService';
import {productService} from "../services/ProductService";
import { SeoFormView } from '../views/SeoFormView';
import { ProductView } from "../views/ProductView";

export class SeoFormComponent {
    private view: SeoFormView;
    private product: ProductView;

    constructor() {
        this.view = new SeoFormView();
        this.product = new ProductView();
    }

    public init(): void {
        this.view.onSendClick(() => this.handleSendClick());
        this.view.onAcceptClick((type: string, value: string) => this.handleAcceptClick(type, value));
    }

    private async handleSendClick(): Promise<void> {
        const requestMessage = this.view.getFormData();
        if (!requestMessage) {
            this.view.renderError('Please select at least one SEO option to generate.');
            return;
        }

        this.view.toggleLoading(true);

        try {
            const response = await agentService.generateSeo(requestMessage);
            if(!response.success) {
                throw new Error(response.message);
            }

            this.view.renderResults(response.data);
        } catch (error: any) {
            this.view.renderError(error.message || 'An unknown error occurred.');
        } finally {
            this.view.toggleLoading(false);
        }
    }

    private async handleAcceptClick(type: string, value: string): Promise<void> {
        this.view.toggleImplementing(true);

        try {
            const response = await productService.updateProductMeta(type, value);
            if(!response.success) {
                throw new Error(response.message);
            }

            this.product.updateProductMeta(type, value);
            this.view.renderSuccess(response.message);
        } catch (error: any) {
            this.view.renderError(error.message || 'An unknown error occurred.');
        } finally {
            this.view.toggleImplementing(false);
        }
    }
}
