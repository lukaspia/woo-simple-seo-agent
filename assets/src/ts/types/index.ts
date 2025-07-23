export interface WpLocalizedParams {
    rest_url: string;
    nonce: string;
    product_id: number;
}

export interface SeoData {
    title?: string;
    description?: string;
    shortDescription?: string;
    keywords?: string;
    summary?: string;
}

export interface ApiError {
    message: string;
}
