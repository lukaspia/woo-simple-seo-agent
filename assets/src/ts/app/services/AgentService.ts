import $ from 'jquery';
import { SeoData, WpLocalizedParams, ApiError } from '../../types';

declare const wssa_params: WpLocalizedParams;

class AgentService {
    public generateSeo(requestMessage: string): Promise<SeoData> {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: wssa_params.rest_url,
                method: 'POST',
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', wssa_params.nonce);
                },
                data: {
                    product_id: wssa_params.product_id,
                    request_message: requestMessage,
                }
            }).done((response) => {
                if (response && response.seo) {
                    resolve(response.seo as SeoData);
                } else {
                    reject({ message: 'Invalid API response structure.' } as ApiError);
                }
            }).fail((jqXHR) => {
                const error: ApiError = jqXHR.responseJSON || { message: 'An unknown error occurred.' };
                reject(error);
            });
        });
    }
}

export const agentService = new AgentService();
