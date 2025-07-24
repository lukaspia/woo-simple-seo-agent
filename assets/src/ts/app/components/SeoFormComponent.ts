import { agentService } from '../services/AgentService';
import {productService} from "../services/ProductService";
import { SeoFormView } from '../views/SeoFormView';

export class SeoFormComponent {
    private view: SeoFormView;

    constructor() {
        this.view = new SeoFormView();
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
            const seoData = await agentService.generateSeo(requestMessage);
            this.view.renderResults(seoData);
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

            this.view.renderSuccess(response.message);
        } catch (error: any) {
            this.view.renderError(error.message || 'An unknown error occurred.');
        } finally {
            this.view.toggleImplementing(false);
        }
    }
}
